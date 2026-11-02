<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('seed_type_id')
                ->constrained('seed_types')
                ->restrictOnDelete();

            $table->unsignedInteger('level')->default(0);
            $table->unsignedInteger('progress')->default(0);
            $table->string('status')->default('ACTIVE');
            $table->unsignedInteger('health')->default(100);

            $table->timestamp('planted_at');
            $table->timestamp('last_cared_at')->nullable();
            $table->timestamp('next_care_at')->nullable();
            $table->timestamp('last_decay_at')->nullable();
            $table->timestamp('harvested_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
