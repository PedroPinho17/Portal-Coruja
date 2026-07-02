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
        Schema::create('school_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('school_name', 255)->comment('Nome da escola');
            $table->text('description')->nullable()->comment('Descrição do protocolo');
            $table->string('document_path', 500)->nullable()->comment('Caminho para o documento do protocolo');
            $table->string('link', 500)->nullable()->comment('Link externo para o protocolo');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->boolean('ativo')->default(true)->comment('Protocolo ativo/inativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_protocols');
    }
};
