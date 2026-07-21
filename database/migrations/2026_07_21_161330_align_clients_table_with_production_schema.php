<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A migration criadora original de `clients` ficou desalinhada da tabela
 * real (que já teve `cpf`/`estado_civil` adicionadas e `raca`/`complemento`/
 * `last_atendimento` removidas fora do controle de migrations, além de
 * várias colunas obrigatórias que a produção já trata como opcionais).
 * Esta migration alinha o schema local ao que a produção realmente é.
 *
 * Usa SQL bruto (MODIFY COLUMN) em vez de Schema::change() porque o
 * pacote doctrine/dbal, necessário para o `->change()` do Laravel, não
 * está instalado neste projeto.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE clients
            MODIFY nome varchar(255) NULL,
            MODIFY sexo varchar(15) NULL,
            MODIFY data_nascimento date NULL,
            MODIFY nacionalidade varchar(15) NULL,
            MODIFY cor varchar(15) NULL,
            MODIFY cep varchar(50) NULL,
            MODIFY logradouro varchar(15) NOT NULL,
            MODIFY endereco varchar(200) NULL,
            MODIFY numero varchar(25) NULL,
            MODIFY bairro varchar(50) NULL,
            MODIFY cidade varchar(50) NULL DEFAULT 'Cuiabá',
            MODIFY uf varchar(2) NULL,
            MODIFY telefone varchar(20) NULL,
            MODIFY email varchar(50) NULL DEFAULT 'Não informado',
            MODIFY tipo_sangue varchar(255) NULL,
            MODIFY create_id datetime NULL,
            MODIFY update_id timestamp NULL,
            MODIFY ativo varchar(1) NULL DEFAULT '1'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE clients
            MODIFY nome varchar(255) NOT NULL,
            MODIFY sexo varchar(15) NOT NULL,
            MODIFY data_nascimento date NOT NULL,
            MODIFY nacionalidade varchar(15) NOT NULL,
            MODIFY cor varchar(15) NOT NULL,
            MODIFY cep varchar(10) NOT NULL,
            MODIFY logradouro varchar(10) NOT NULL,
            MODIFY endereco varchar(200) NOT NULL,
            MODIFY numero varchar(6) NOT NULL,
            MODIFY bairro varchar(100) NOT NULL,
            MODIFY cidade varchar(50) NOT NULL,
            MODIFY uf varchar(2) NOT NULL,
            MODIFY telefone varchar(20) NOT NULL,
            MODIFY email varchar(50) NOT NULL,
            MODIFY tipo_sangue varchar(255) NOT NULL,
            MODIFY create_id datetime NOT NULL,
            MODIFY update_id datetime NOT NULL,
            MODIFY ativo varchar(1) NOT NULL
        ");
    }
};
