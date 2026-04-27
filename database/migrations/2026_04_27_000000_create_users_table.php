<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 60);
            $table->string('usuario', 30)->unique();
            $table->string('email', 35)->unique();
            $table->string('password');
            $table->string('biografia', 150)->nullable();
            $table->string('foto_url')->nullable();
            $table->boolean('ativo')->default(true);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};