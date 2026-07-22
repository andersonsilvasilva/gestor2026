<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * O SUS extinguiu o Cartão Nacional de Saúde (CNS) como identificador de
 * controle, substituído pelo CPF. CNS deixa de ser obrigatório (mas o
 * campo continua existindo para quem já tem o cartão cadastrado); CPF
 * passa a ser o identificador usado a partir de agora.
 *
 * NÃO adiciona um índice único de verdade no banco: a base real tem ~740
 * clientes e pelo menos 7 pares com o mesmo CPF válido, que parecem ser o
 * mesmo paciente cadastrado duas vezes (nome/nascimento batendo, CNS com
 * 1 dígito de diferença) - decisão de mesclar ou não é humana, não deve
 * travar esta migration. Unicidade de CPF passa a ser garantida só na
 * validação da aplicação a partir de agora (impede novos duplicados);
 * ver item correspondente em MELHORIAS.md para o cadastro retroativo.
 *
 * Usa SQL bruto (MODIFY COLUMN) em vez de Schema::change() porque o
 * doctrine/dbal, necessário pro ->change() do Laravel, não está instalado
 * neste projeto (mesmo padrão de align_clients_table_with_production_schema).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE clients MODIFY cns varchar(20) NULL');

        // placeholders usados historicamente quando o CPF não era exigido,
        // nunca foram CPF de verdade - normaliza pra NULL
        DB::table('clients')->whereIn('cpf', ['', '000.000.000-00', '999.999.999-99'])->update(['cpf' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE clients MODIFY cns varchar(20) NOT NULL');
    }
};
