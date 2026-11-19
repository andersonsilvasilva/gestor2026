<x-layout-app page-title="Licença e Privacidade">
    <style>
        .lic-hero {
            background: linear-gradient(135deg, #1f2937 0%, #0f766e 100%);
            color: #fff; border-radius: 14px; padding: 22px 26px;
        }
        .lic-hero .lic-icon {
            width: 52px; height: 52px; border-radius: 12px; background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;
        }
        .lic-hero small { color: rgba(255,255,255,.75); }
        .lic-badge {
            background: rgba(34,197,94,.2); color: #bbf7d0; border: 1px solid rgba(34,197,94,.45);
            font-size: 12px; font-weight: 500; padding: 4px 10px; border-radius: 20px; white-space: nowrap;
        }
        .lic-section { border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; }
        .lic-section > .lic-section-title {
            padding: 16px 22px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #0f766e;
        }
        .lic-card { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; height: 100%; }
        .lic-card .lic-label { font-size: 10.5px; letter-spacing: .06em; text-transform: uppercase; color: #6b7280; margin-bottom: 2px; }
        .lic-card .lic-value { font-size: 14px; font-weight: 600; color: #111827; }
        .lic-link { color: #0f766e; text-decoration: none; border-bottom: 1px dotted #0f766e; }
        .lic-link:hover { color: #115e59; border-bottom-style: solid; }
        .lic-clause { display: flex; gap: 12px; padding: 12px 0; border-top: 1px solid #f1f5f9; }
        .lic-clause:first-of-type { border-top: 0; }
        .lic-clause h6 { margin-bottom: 4px; font-size: 14px; }
        .lic-clause p { margin: 0; font-size: 13.5px; color: #374151; line-height: 1.6; }
        .lic-num {
            width: 30px; height: 30px; border-radius: 50%; background: #e6f4f1; color: #0f766e;
            font-size: 13px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .lic-note { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 10px; font-size: 13px; padding: 10px 14px; }
    </style>

    <div class="container-fluid px-4 pb-5" style="max-width: 1000px;">

        <div class="lic-hero d-flex align-items-center gap-3 mb-4">
            <div class="lic-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="flex-grow-1">
                <h4 class="mb-0">Licença de Uso e Privacidade</h4>
                <small>Titularidade, licenciamento e tratamento de dados pessoais</small>
            </div>
            <span class="lic-badge d-none d-sm-inline"><i class="fas fa-circle-check me-1"></i>Licença ativa</span>
        </div>

        {{-- LICENÇA --}}
        <div class="lic-section mb-4">
            <div class="lic-section-title"><i class="fas fa-file-contract me-2"></i>Licença de Uso</div>
            <div class="p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><div class="lic-card">
                        <div class="lic-label">Produto</div>
                        <div class="lic-value">URPICS - Gestor de Prontuários</div>
                    </div></div>
                    <div class="col-md-6"><div class="lic-card">
                        <div class="lic-label">Titular</div>
                        <div class="lic-value"><a href="https://hightechtecnologia.com.br/#cases" target="_blank" rel="noopener noreferrer" class="lic-link">HighTech Tecnologia Ltda.</a></div>
                    </div></div>
                    <div class="col-md-6"><div class="lic-card">
                        <div class="lic-label">CNPJ do titular</div>
                        <div class="lic-value">61.693.043/0001-94</div>
                    </div></div>
                    <div class="col-md-3 col-6"><div class="lic-card">
                        <div class="lic-label">Modalidade</div>
                        <div class="lic-value">Não exclusiva</div>
                    </div></div>
                    <div class="col-md-3 col-6"><div class="lic-card">
                        <div class="lic-label">Versão</div>
                        <div class="lic-value">{{ config('app.version', '2.0.2026') }}</div>
                    </div></div>
                </div>

                <div class="lic-clause"><span class="lic-num">1</span><div>
                    <h6>Objeto da licença</h6>
                    <p>Autoriza a unidade usuária a utilizar o sistema URPICS - Gestor de Prontuários, de forma não exclusiva e intransferível, exclusivamente para a gestão de prontuários, agendamentos e atendimentos da própria unidade.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num">2</span><div>
                    <h6>Propriedade intelectual</h6>
                    <p>A licença concedida não representa venda, cessão ou transferência da plataforma, de seu código-fonte, marca ou propriedade intelectual, que permanecem com a HighTech Tecnologia Ltda.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num">3</span><div>
                    <h6>Restrições de uso</h6>
                    <p>É vedado copiar, modificar, redistribuir, sublicenciar, realizar engenharia reversa ou disponibilizar o sistema a terceiros não autorizados.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num">4</span><div>
                    <h6>Responsabilidade pelo acesso</h6>
                    <p>Cada usuário é responsável pela guarda de suas credenciais e pelas ações realizadas com elas. O acesso deve ser concedido apenas a pessoas autorizadas pela unidade.</p>
                </div></div>
            </div>
        </div>

        {{-- LGPD --}}
        <div class="lic-section">
            <div class="lic-section-title"><i class="fas fa-user-shield me-2"></i>LGPD e Política de Privacidade</div>
            <div class="p-4">
                <div class="lic-note mb-3">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    Este sistema trata <strong>dados pessoais sensíveis referentes à saúde</strong>, em conformidade com a
                    Lei Geral de Proteção de Dados (Lei nº 13.709/2018 — LGPD).
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6"><div class="lic-card">
                        <div class="lic-label">Controladora</div>
                        <div class="lic-value">Secretaria Municipal de Saúde de Cuiabá - SMS Cuiabá</div>
                    </div></div>
                    <div class="col-md-6"><div class="lic-card">
                        <div class="lic-label">Operadora</div>
                        <div class="lic-value"><a href="https://hightechtecnologia.com.br/#cases" target="_blank" rel="noopener noreferrer" class="lic-link">HighTech Tecnologia Ltda.</a></div>
                    </div></div>
                </div>

                <div class="lic-clause"><span class="lic-num"><i class="fas fa-database"></i></span><div>
                    <h6>Dados tratados e finalidade</h6>
                    <p>Dados cadastrais (nome, CPF, contato, endereço), dados de atendimento e prontuário. A finalidade é exclusivamente a prestação da assistência à saúde, o agendamento e a gestão administrativa do atendimento.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num"><i class="fas fa-scale-balanced"></i></span><div>
                    <h6>Base legal</h6>
                    <p>Tutela da saúde, em procedimento realizado por profissionais de saúde, e cumprimento de obrigação legal ou regulatória (art. 7º e art. 11 da LGPD).</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num"><i class="fas fa-lock"></i></span><div>
                    <h6>Segurança</h6>
                    <p>São adotadas medidas técnicas e administrativas para proteger os dados contra acessos não autorizados, perda ou alteração, incluindo controle de acesso por perfil e autenticação por usuário.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num"><i class="fas fa-share-nodes"></i></span><div>
                    <h6>Compartilhamento</h6>
                    <p>Os dados não são vendidos nem compartilhados com terceiros, salvo por obrigação legal ou determinação de autoridade competente.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num"><i class="fas fa-user-check"></i></span><div>
                    <h6>Direitos do titular</h6>
                    <p>O titular pode solicitar confirmação de tratamento, acesso, correção, anonimização, portabilidade e informações sobre o uso de seus dados, dirigindo-se à unidade de saúde (controladora) ou ao encarregado pelo tratamento de dados.</p>
                </div></div>
                <div class="lic-clause"><span class="lic-num"><i class="fas fa-clock-rotate-left"></i></span><div>
                    <h6>Retenção</h6>
                    <p>Os dados são mantidos pelo prazo exigido pela legislação aplicável aos prontuários de saúde e pelo tempo necessário à finalidade do tratamento.</p>
                </div></div>
            </div>
        </div>

        <p class="text-muted small mt-3 mb-0"><i class="fas fa-building me-1"></i><a href="https://hightechtecnologia.com.br/#cases" target="_blank" rel="noopener noreferrer" class="lic-link">HighTech Tecnologia Ltda.</a> — CNPJ 61.693.043/0001-94</p>
    </div>
</x-layout-app>
