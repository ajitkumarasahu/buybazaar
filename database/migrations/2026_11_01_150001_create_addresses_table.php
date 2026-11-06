<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 30)->nullable();
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->string('line1', 150);
            $table->string('line2', 150)->nullable();
            $table->string('city', 80);
            $table->string('state', 80);
            $table->string('postal_code', 12);
            $table->char('country', 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};