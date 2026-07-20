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
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome_instituicao')->default('SECRETÁRIA MUNICIPAL DE SAÚDE DE CUIABÁ');
            $table->string('nome_secretaria')->default('COORDENADORIA DE ATENÇÃO SECUNDÁRIA');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // Mantém os relatórios exatamente como estão hoje até o admin alterar.
        DB::table('configuracoes')->insert([
            'nome_instituicao' => 'SECRETÁRIA MUNICIPAL DE SAÚDE DE CUIABÁ',
            'nome_secretaria'  => 'COORDENADORIA DE ATENÇÃO SECUNDÁRIA',
            'logo_path'        => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
};
