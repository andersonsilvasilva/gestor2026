## Gestor de Prontuários de Atendimentos Ambulatoriais

**Autor:** Anderson Antônio Barros e Silva
**URPICS Cuiabá — versão 2.0.2026**

Sistema de gestão de prontuários e agendamentos para uma unidade de saúde (URPICS), usado por administradores, responsáveis técnicos, secretaria e colaboradores (terapeutas) via papéis de acesso distintos.

## Funcionalidades

- **Clientes/pacientes** — cadastro, edição, exclusão (soft delete) e restauração.
- **Agenda** — criação/edição de atendimentos por colaborador, com histórico de atendimentos por cliente ("Listar Atendimentos").
- **Departamentos, Procedimentos e Colaboradores** — CRUD com soft delete, inativação (sem apagar histórico) e proteção de registros de referência.
- **Gestão de Usuários** — contas de acesso (Admin, Responsável Técnico, Secretaria), separada da tela de Colaboradores (papel RH).
- **Papéis e permissões** — 4 papéis (`admin`, `rt`, `sec`, `rh`), cada um com visibilidade e ações distintas (ver Gates em `AppServiceProvider`).
- **Configurações** — nome da instituição/secretaria/unidade de saúde e logo, usados no cabeçalho dos relatórios oficiais.
- **Relatórios** — BPA-I, Mapa de Produção (mensal e diário) e AVEIANM, gerados com JasperReports 7 por um microsserviço externo (ver [Relatórios](#relatórios) abaixo).

## Requisitos

- PHP 8.2 ou superior
- Composer
- Node.js 20 ou superior
- MySQL 8 / MariaDB (produção usa MariaDB 11.8 na Hostinger)

## Instalação local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure `DB_*` no `.env` apontando para um banco local (crie o banco antes, ex. `gestor`), depois:

```bash
php artisan migrate
php artisan db:seed
npm run build
```

**Importante:** rode o projeto via Apache (`http://gestor.test` ou equivalente no Laragon/XAMPP), **não** `php artisan serve` — funcionalidades que dependem de sessão/cookies e de chamadas ao serviço de relatórios se comportam diferente sob o servidor embutido.

### Testes automatizados

Use um banco dedicado (nunca o de desenvolvimento), configurado em `.env.testing`:

```bash
php artisan test
```

## Relatórios

Os 4 relatórios em JasperReports 7 (BPA-I, Mapa de Produção mensal/diário, AVEIANM) dependem de Java, que **não está disponível no ambiente de produção (Hostinger)**. A geração roda num microsserviço Node/Express separado (`reports-service/`), hospedado num VPS, chamado via HTTP autenticado por segredo compartilhado (`REPORTS_SERVICE_URL` / `REPORTS_SERVICE_SECRET` no `.env`). Localmente, em desenvolvimento, o motor Java roda direto na máquina (ver `storage/app/jasperstarter7/`, não versionado).

Para detalhes de configuração do motor JasperReports 7 e do microsserviço do VPS (instalação de Java, fontes, deploy do serviço), ver a documentação interna do projeto.

## Deploy em produção

Deploy manual via FTP/Gerenciador de Arquivos (Hostinger não oferece SSH confiável). O passo a passo completo — arquivos a enviar, migrations a rodar via phpMyAdmin, e o serviço de relatórios no VPS — está em `DEPLOY.md`.
