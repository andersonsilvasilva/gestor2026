<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['raca', 'complemento', 'last_atendimento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('raca', 10)->nullable()->after('nacionalidade');
            $table->string('complemento', 15)->nullable()->after('numero');
            $table->date('last_atendimento')->nullable()->after('observacao');
        });
    }
};
