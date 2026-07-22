# Guia de deploy — Hostinger (urpics-cuiaba.net)

Deploy manual via FTP/File Manager. **Não use o "Implante de GitHub" do hPanel** — ele só mira dentro de `public_html` e pode sobrescrever arquivos não versionados (`.env`), conforme confirmado pela própria IA da Hostinger.

Estrutura real em produção (confirmada via backup baixado):
- `public_html/` = conteúdo da pasta `public/` do Laravel (a raiz web).
- `urpics_projeto_files/` = o app real (`app/`, `vendor/`, `.env`, etc.), fora da raiz web.

## 0. Backup antes de mexer

Já feito: `.env` de produção baixado, e os dois zips (`public_html.zip`, `urpics_projeto_files.zip`) salvos em `D:\dev\Gestor_2025\`. Não prossiga sem isso se ainda não tiver.

## 1. Banco de dados — rodar via phpMyAdmin (hPanel)

As migrations deste projeto se dividem em dois grupos. **Só o grupo A precisa rodar em produção.**

**Grupo A — genuinamente novo, rodar em produção:**
- `create_configuracoes_table`
- `add_unidade_saude_to_configuracoes_table`
- `add_ativo_to_users_table`
- `add_deleted_at_to_procediments_table` *(soft delete de Procedimento — ver MELHORIAS.md)*

**Grupo B — apenas "catch-up", já refletem o schema real de produção, NÃO rodar:**
- `add_cpf_and_estado_civil_to_clients_table`
- `remove_obsolete_columns_from_clients_table`
- `align_clients_table_with_production_schema`

Antes de tudo, no phpMyAdmin, rode para saber o próximo número de batch:

```sql
SELECT MAX(batch) FROM migrations;
```

Anote o valor (chame de `N`). Depois rode o SQL abaixo, trocando `{N+1}` pelo valor real:

```sql
-- 1) tabela configuracoes (com unidade_saude já incluída)
CREATE TABLE `configuracoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome_instituicao` varchar(255) NOT NULL DEFAULT 'SECRETÁRIA MUNICIPAL DE SAÚDE DE CUIABÁ',
  `nome_secretaria` varchar(255) NOT NULL DEFAULT 'COORDENADORIA DE ATENÇÃO SECUNDÁRIA',
  `unidade_saude` varchar(255) NOT NULL DEFAULT 'URPICS- UNIDADE DE REFERÊNCIA EM PRÁTICAS INTEGRATIVAS E COMPLEMENTARES EM SAUDE',
  `logo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuracoes` (`nome_instituicao`, `nome_secretaria`, `unidade_saude`, `logo_path`, `created_at`, `updated_at`)
VALUES ('SECRETÁRIA MUNICIPAL DE SAÚDE DE CUIABÁ', 'COORDENADORIA DE ATENÇÃO SECUNDÁRIA', 'URPICS- UNIDADE DE REFERÊNCIA EM PRÁTICAS INTEGRATIVAS E COMPLEMENTARES EM SAUDE', NULL, NOW(), NOW());

-- 2) coluna ativo em users (colaborador inativo deixa de aparecer nos seletores)
ALTER TABLE `users` ADD COLUMN `ativo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `role`;

-- 3) coluna deleted_at em procediments (soft delete de Procedimento)
ALTER TABLE `procediments` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;

-- 4) registrar as 4 migrations como já aplicadas (troque {N+1} pelo valor anotado acima)
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_07_20_204140_create_configuracoes_table', {N+1}),
('2026_07_20_212002_add_unidade_saude_to_configuracoes_table', {N+1}),
('2026_07_21_141025_add_ativo_to_users_table', {N+1}),
('2026_07_22_130601_add_deleted_at_to_procediments_table', {N+1});
```

Não rode `php artisan migrate` direto em produção — sem terminal confiável, é mais seguro aplicar o SQL equivalente manualmente e só registrar no `migrations` que já rodou.

## 2. Arquivos para enviar (FTP, dentro de `urpics_projeto_files` e `public_html`)

### 2a. Novos (não existem em produção — enviar como arquivo novo)
- `app/Http/Controllers/ConfiguracaoController.php`
- `app/Http/Controllers/UserController.php`
- `app/Models/Configuracao.php`
- `resources/views/configuracoes/edit.blade.php`
- `resources/views/usuarios/*.blade.php` (todas)
- `database/migrations/2026_07_20_204140_create_configuracoes_table.php`
- `database/migrations/2026_07_20_212002_add_unidade_saude_to_configuracoes_table.php`
- `database/migrations/2026_07_21_141025_add_ativo_to_users_table.php`
- `database/migrations/2026_07_22_130601_add_deleted_at_to_procediments_table.php`
- `public/assets/js/custom2.js` (usado pelo novo `agenda/index.blade.php`)

### 2b. Substituir (arquivos que já existem em produção — sobrescrever)
- `routes/web.php`
- `app/Http/Controllers/AgendaController.php`
- `app/Http/Controllers/ListarAtender.php` *(só o fix de `whereNull('eventos.deleted_at')`; produção já tem o arquivo, mas sem esse fix)*
- `app/Http/Controllers/ProcedimentoControl.php` *(soft delete: `withTrashed()` no index, `restoreProcedimento`)*
- `app/Models/Procediment.php` *(SoftDeletes)*
- `resources/views/procedimento/procedimentos.blade.php` *(botão Restaurar)*
- `app/Http/Controllers/RhUserController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/ConfirmAccountController.php`
- `app/Http/Controllers/ControllerReport.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/JasperController.php`
- `app/Http/Controllers/ClienteController.php` *(versão mesclada: base produção + fix do `deleteClienteConfirm`)*
- `app/Http/Requests/ContaRequest.php` *(igual produção — sem mudança real, mas confirme)*
- `app/Models/Department.php`
- `app/Models/Procediment.php`
- `app/Models/User.php`
- `app/Models/eventos.php`
- `app/Providers/AppServiceProvider.php`
- `resources/views/components/layout-app.blade.php` *(mesclado: Alpine.js + modais de produção + meu CSS de impressão)*
- `resources/views/components/side-bar.blade.php`
- `resources/views/clientes/cliente.blade.php` *(mesclado: base produção + botão Restaurar em POST)*
- `resources/views/clientes/delete-clientes-confirm.blade.php`
- `resources/views/colaborators/rh-users.blade.php`
- `resources/views/colaborators/delete-rh-user.blade.php`
- `resources/views/department/delete-department-confirm.blade.php`
- `resources/views/procedimento/procedimentos.blade.php`
- `resources/views/procedimento/delete-procedimento-confirm.blade.php`
- `resources/views/home.blade.php`
- `resources/views/relatorio/gerador.blade.php`
- `resources/views/agenda/index.blade.php`

### 2c. Restaurados da produção real — meu repo tinha cópias desatualizadas, já corrigi localmente, mas **produção já tem a versão certa, não precisa enviar**
- `resources/views/clientes/add-cliente.blade.php`
- `resources/views/clientes/edita-clientes.blade.php`
- `resources/views/clientes/show-clientes.blade.php`
- `resources/views/colaborators/add-rh-user.blade.php`
- `app/Http/Controllers/DepartmentController.php` *(confirmado idêntico ao de produção)*
- `app/View/Components/ModalEvento.php`, `ModalEventoVer.php`
- `resources/views/components/modal-evento.blade.php`, `modal-evento-ver.blade.php`
- `resources/views/clientes/listar.blade.php`

### 2d. Não enviar / não mexer
- Os 7 `.jrxml` dos relatórios novos (Jasper 7) — não fazem sentido em produção ainda, pois **Java não está disponível na Hostinger** (confirmado). Ficam pendentes até o VPS de relatórios existir.
- `storage/app/jasperstarter7/` — mesma razão.
- Qualquer coisa em `.env` — nunca sobrescrever o `.env` real de produção.

## 3. Depois do upload

1. Confirmar que `storage/` e `bootstrap/cache/` continuam com permissão de escrita.
2. Rodar (via algum meio disponível — terminal do hPanel se existir, ou renomear temporariamente para forçar) `php artisan config:clear` e `php artisan view:clear`/`cache:clear`, já que trocamos bastante Blade e rotas. Se não houver terminal, apagar manualmente o conteúdo de `storage/framework/views/*` e `bootstrap/cache/*.php` (exceto `.gitignore`) resolve o cache de views/config.
3. Testar como usuário real: login, Clientes (listar/criar/editar/apagar/restaurar), Agenda (criar evento arrastando e pelo botão), Listar Atendimentos (History), Departamentos/Procedimentos/Colaboradores (users com role `rt`/`sec` vendo o que devem ver), Configurações e Gestão de Usuários (só admin).

## 4. Depois do deploy (não urgente)

- Relatórios Jasper 7 (BPA-I, Mapa de Produção, AVEIANM) e VPS de Java — sessão separada, DNS/subdomínio apontando pro VPS existente, conectando no mesmo banco da Hostinger.
- Limpar o `env_copia` que existe hoje na pasta real do app em produção (não é acessível pela web, mas tem senhas em texto puro).
