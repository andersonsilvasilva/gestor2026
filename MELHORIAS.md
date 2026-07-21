# Checklist de Melhorias — Gestor

Levantamento inicial feito a partir de leitura do código (models, controllers, rotas, migrations, config). Marque os itens conforme forem resolvidos.

## 🔴 Bugs que quebram funcionalidade (corrigir primeiro)

- [x] **Relacionamentos Eloquent sem `return`** — `app/Models/User.php`, `app/Models/Client.php`, `app/Models/Procediment.php`, `app/Models/Department.php`, `app/Models/eventos.php`. Corrigido: adicionado `return` e trocado `belongsToMany` (que assumia pivot table inexistente) por `belongsTo`/`hasMany` de acordo com as FKs reais do schema (`user_id`, `client_id`, `procediments_id`, `department_id`). Validado via tinker contra o banco local.
- [x] **`AgendaController::mostraDados`** — corrigido para usar as relações `Procediment_eventos`/`user_eventos` (agora funcionais) em vez de tratar as FKs inteiras como objetos. Retorno do JSON também corrigido (estava passando parâmetros errados para `response()->json()`).
- [x] **`AdminController`** — removido (código morto, não roteado, referenciava models inexistentes). Indicadores de admin já são cobertos pelo `DashboardController`.
- [x] **`BookController`** — removido junto com `app/Imports/BookImport.php` (dependia de pacote não instalado, não roteado, view inexistente). Import de planilha vira item de feature futura, se necessário.
- [x] **`JasperController::console()`** — método removido (caminho absoluto Windows hardcoded, só usado para teste manual). Resto da classe mantido (usado por `ControllerReport`).
- [x] Validação com typo em `AgendaController::store`: `max255` → `max:255`.
- [x] **XSS armazenado em potencial** nos gráficos do dashboard — `DashboardController` agora monta arrays PHP normais (em vez de strings pré-formatadas com aspas manuais), e `home.blade.php` usa `@json()` em vez de `{!! !!}` para os labels e dados dos gráficos Chart.js.

## 🟠 Segurança

- [ ] **Nenhuma autorização por papel está ativa.** Todos os `Auth::user()->can('admin')` estão comentados em todos os controllers (`DepartmentController`, `ClienteController`, `ProcedimentoControl`, `RhUserController` etc). Hoje, **qualquer usuário autenticado acessa qualquer rota**, inclusive criar/editar/excluir colaboradores, departamentos, clientes. `bootstrap/app.php` também não registra nenhum middleware customizado.
- [ ] **Operações destrutivas via GET** — excluir cliente, colaborador, departamento, procedimento e evento são todos `Route::get(...delete...)`. Isso é vulnerável a CSRF por link/prefetch de navegador/crawler, além de fugir do padrão REST. Deveriam ser `POST`/`DELETE` com `@csrf`.
- [ ] Sem **Policies/Gates** definidos (`app/Policies` não existe) — não há uma fonte única de verdade para "quem pode fazer o quê". Isso deveria ser implementado junto com a reativação das checagens de admin.
- [ ] Política de senha inconsistente: `ConfirmAccountController` exige maiúscula+minúscula+dígito (`app/Http/Controllers/ConfirmAccountController.php:26`), mas `ProfileController::updatePassword` só exige `min:8|max:16` sem complexidade.
- [ ] `max:16` em senha é incomum e limitante (bcrypt suporta até 72 bytes) — vale reavaliar esse limite máximo.
- [ ] Credenciais de produção (DB, SMTP) estão em texto puro no `.env` do projeto (esperado para Laravel, mas confirme que backups/`.env.production` nunca vão para controle de versão ou para fora da máquina local).
- [ ] Sem `EnsureEmailIsVerified` aplicado nas rotas, mesmo com `Features::emailVerification()` habilitado no Fortify — o recurso está ligado mas não é de fato exigido em nenhum lugar.

## 🟡 Qualidade de código / dívida técnica

- [ ] **Zero testes reais** — só existem os `ExampleTest.php` padrão do Laravel (`tests/Feature`, `tests/Unit`). Nenhuma cobertura para clientes, agenda, colaboradores, relatórios.
- [x] Duplicação grande em `DashboardController::index` — resolvido ao implementar os controles de período/tipo de gráfico do dashboard: os dois branches agora usam um único `buildChartData()` privado parametrizado por `user_id`.
- [ ] Uso de `client::` (minúsculo) em vez de `Client::` em `ClienteController.php:92` — funciona no PHP (case-insensitive para nomes de classe), mas é inconsistente e confunde leitura/autocomplete.
- [x] Uso de `$_POST` diretamente em `ControllerReport::generateReport` — resolvido na reescrita do controller para o novo sistema de relatórios (agora usa `Request::validate()`).
- [ ] Vários blocos de código comentado (relatórios BPA1/mapa de produção, autorizações, chamadas alternativas ao PHPJasper) — limpar ou implementar.
- [ ] Rotas sem padronização: mistura `snake-case`, nomes inconsistentes. `routes/web.php:56` tem uma chave sobrando na URL: `/rh-users/edita-colaborator}` — barra de fechamento `}` foi commitada por engano.
- [ ] `ProfileController::updatePassword` usa `password_verify` direto em vez do `Hash::check` do Laravel (funciona, mas foge do padrão/facade usado no resto do projeto).

## 🔵 Infraestrutura / configuração

- [ ] Comentário do `.env` diz "Hostinger Mysql 8.4", mas o dump revelou **MariaDB 11.8** rodando de fato — corrigir o comentário e verificar se algum recurso específico de MySQL é usado sem querer.
- [ ] Sem pipeline de CI (não há `.github/workflows`) — nenhuma checagem automática de testes/lint antes de merge.
- [ ] Sem `.env.example` atualizado a refletir todas as chaves reais usadas (`JASPER_DRIVER`, driver paths do Jasper etc. — vale conferir se estão documentadas).
- [ ] Geração de relatórios depende de Java + JasperStarter instalado no servidor (`geekcom/phpjasper`) — não documentado no README como dependência de deploy.

## 🟢 Produto / funcionalidades a considerar

- [x] Módulo de relatórios Jasper — BPA-I, Mapa de Produção (mensal e diário) e AVEIANM implementados de ponta a ponta (engine JasperReports 7, ver `[[project_reports_jasper7_engine]]`), com filtro por colaborador/admin, nome de arquivo padronizado e configuração de instituição/secretaria/logo/unidade de saúde pelo admin.
- [ ] Import de planilha de clientes/pacientes (`BookController` foi removido por ser código morto) — ainda não definimos o formato/dados; retomar se virar prioridade.
- [ ] Nenhuma tela de auditoria/log de quem alterou o quê (prontuário de paciente é dado sensível — LGPD relevante para um sistema de saúde).
