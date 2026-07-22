<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mesma mudança de política de identificação do SUS aplicada em `clients`
 * (ver make_cns_nullable_and_cpf_unique_on_clients) - CPF passa a ser o
 * identificador do colaborador/profissional; CNS vira opcional.
 * `user_details` nunca teve coluna `cpf`.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->unique()->after('cns');
        });

        DB::statement('ALTER TABLE user_details MODIFY cns varchar(20) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE user_details MODIFY cns varchar(20) NOT NULL');

        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
