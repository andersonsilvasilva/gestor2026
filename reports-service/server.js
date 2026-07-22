require('dotenv').config();

const express = require('express');
const { spawn } = require('child_process');
const fs = require('fs/promises');
const fssync = require('fs');
const path = require('path');
const crypto = require('crypto');
const os = require('os');

const app = express();
app.use(express.json({ limit: '1mb' }));

const PORT = process.env.PORT || 4000;
const SHARED_SECRET = process.env.REPORTS_SHARED_SECRET || '';

const BIN_DIR = process.env.JASPERSTARTER_BIN_DIR || path.join(__dirname, 'jasperstarter7/bin');
const SOURCE_DIR = process.env.REPORTS_SOURCE_DIR || path.join(__dirname, 'reports/source/MyReports');
const COMPILED_DIR = process.env.REPORTS_COMPILED_DIR || path.join(__dirname, 'reports/compiled');
const OUTPUT_DIR = process.env.REPORTS_OUTPUT_DIR || path.join(__dirname, 'reports/output');
const DEFAULT_LOGO_PATH = path.join(BIN_DIR, 'images/logo_prefeitura.png');

// Mesma lista/regra de app/Http/Controllers/ControllerReport.php no Gestor -
// se um relatorio for adicionado la, precisa ser adicionado aqui tambem.
const REPORTS = ['aveianm', 'bpa1', 'mapa_producao', 'mapa_producao_diario'];

const DB_URL = `jdbc:mysql://${process.env.DB_HOST}:${process.env.DB_PORT || '3306'}/${process.env.DB_DATABASE}?sessionVariables=sql_mode=''`;

function timingSafeEqualStrings(a, b) {
    const bufA = Buffer.from(a);
    const bufB = Buffer.from(b);
    if (bufA.length !== bufB.length) return false;
    return crypto.timingSafeEqual(bufA, bufB);
}

function requireAuth(req, res, next) {
    const header = req.get('authorization') || '';
    const token = header.startsWith('Bearer ') ? header.slice(7) : '';
    if (!SHARED_SECRET || !token || !timingSafeEqualStrings(token, SHARED_SECRET)) {
        return res.status(401).json({ error: 'unauthorized' });
    }
    next();
}

function isValidParametro(p) {
    // "chave=valor", chave restrita a letras/numeros/underscore (mesmo formato usado no ControllerReport.php)
    return typeof p === 'string' && p.length <= 2000 && /^[A-Za-z_][A-Za-z0-9_]*=/.test(p);
}

async function ensureCompiled(report) {
    const jasperPath = path.join(COMPILED_DIR, report + '.jasper');
    const jrxmlPath = path.join(SOURCE_DIR, report + '.jrxml');

    if (fssync.existsSync(jasperPath)) return jasperPath;
    if (!fssync.existsSync(jrxmlPath)) return null;

    await runJasperstarter(['compile', jrxmlPath, '-o', COMPILED_DIR], 60000);

    return fssync.existsSync(jasperPath) ? jasperPath : null;
}

function runJasperstarter(args, timeoutMs) {
    return new Promise((resolve, reject) => {
        const child = spawn(path.join(BIN_DIR, 'jasperstarter.sh'), args, { cwd: BIN_DIR });

        let stdout = '';
        let stderr = '';
        const timer = setTimeout(() => {
            child.kill('SIGKILL');
            reject(new Error('timeout'));
        }, timeoutMs);

        child.stdout.on('data', (d) => { stdout += d; });
        child.stderr.on('data', (d) => { stderr += d; });

        child.on('error', (err) => {
            clearTimeout(timer);
            reject(err);
        });

        child.on('close', (code) => {
            clearTimeout(timer);
            if (code !== 0) {
                reject(new Error(`jasperstarter exited with code ${code}: ${stderr || stdout}`));
                return;
            }
            resolve({ stdout, stderr });
        });
    });
}

async function resolveLogoPath(logoUrl, tempFiles) {
    if (!logoUrl) return DEFAULT_LOGO_PATH;
    if (!/^https:\/\//.test(logoUrl)) return DEFAULT_LOGO_PATH;

    try {
        const response = await fetch(logoUrl, { signal: AbortSignal.timeout(10000) });
        if (!response.ok) return DEFAULT_LOGO_PATH;

        const ext = path.extname(new URL(logoUrl).pathname) || '.png';
        const tempPath = path.join(os.tmpdir(), `logo_${crypto.randomUUID()}${ext}`);
        const buffer = Buffer.from(await response.arrayBuffer());
        await fs.writeFile(tempPath, buffer);
        tempFiles.push(tempPath);

        return tempPath;
    } catch (err) {
        console.error('Falha ao baixar logo, usando padrao:', err.message);
        return DEFAULT_LOGO_PATH;
    }
}

app.post('/gerar', requireAuth, async (req, res) => {
    const { relatorio, parametros, incluir_logo: incluirLogo, logo_url: logoUrl } = req.body || {};

    if (!REPORTS.includes(relatorio)) {
        return res.status(422).json({ error: 'relatorio invalido' });
    }
    if (!Array.isArray(parametros) || !parametros.every(isValidParametro)) {
        return res.status(422).json({ error: 'parametros invalidos' });
    }

    const tempFiles = [];
    let outputPdf = null;

    try {
        const jasperPath = await ensureCompiled(relatorio);
        if (!jasperPath) {
            return res.status(404).json({ error: 'relatorio nao encontrado no servidor de relatorios' });
        }

        const outputBasename = `${relatorio}_${crypto.randomUUID()}`;
        outputPdf = path.join(OUTPUT_DIR, outputBasename + '.pdf');

        const logoParam = [];
        if (incluirLogo) {
            const logoPath = await resolveLogoPath(logoUrl, tempFiles);
            logoParam.push(`LOGO_PATH=${logoPath}`);
        }

        const args = [
            'process',
            jasperPath,
            '-o', path.join(OUTPUT_DIR, outputBasename),
            '-f', 'pdf',
            '-P',
            ...parametros,
            ...logoParam,
            '-t', 'generic',
            '--db-driver', 'com.mysql.cj.jdbc.Driver',
            '--db-url', DB_URL,
            '-u', process.env.DB_USERNAME,
        ];
        if (process.env.DB_PASSWORD) {
            args.push('-p', process.env.DB_PASSWORD);
        }

        await runJasperstarter(args, 120000);

        if (!fssync.existsSync(outputPdf)) {
            throw new Error('PDF nao foi gerado');
        }

        const pdf = await fs.readFile(outputPdf);
        res.set('Content-Type', 'application/pdf');
        res.send(pdf);
    } catch (err) {
        console.error(`Falha ao gerar relatorio ${relatorio}:`, err.message);
        res.status(500).json({ error: 'Nao foi possivel gerar o relatorio.' });
    } finally {
        tempFiles.push(outputPdf);
        for (const f of tempFiles) {
            if (f) fs.unlink(f).catch(() => {});
        }
    }
});

app.get('/health', (req, res) => res.json({ ok: true }));

app.listen(PORT, () => {
    console.log(`Servico de relatorios ouvindo na porta ${PORT}`);
});
