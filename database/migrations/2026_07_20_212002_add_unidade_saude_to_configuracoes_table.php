<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->string('unidade_saude')->default('URPICS- UNIDADE DE REFERÊNCIA EM PRÁTICAS INTEGRATIVAS E COMPLEMENTARES EM SAUDE')->after('nome_secretaria');
        });

        // Mantém os relatórios exatamente como estão hoje até o admin alterar.
        DB::table('configuracoes')->update([
            'unidade_saude' => 'URPICS- UNIDADE DE REFERÊNCIA EM PRÁTICAS INTEGRATIVAS E COMPLEMENTARES EM SAUDE',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropColumn('unidade_saude');
        });
    }
};
