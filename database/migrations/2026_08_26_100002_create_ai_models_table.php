<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('name'); // display name, e.g. "GPT-4.1"
            $table->string('model_key'); // the exact string sent to the provider's API, e.g. "gpt-4.1"
            $table->decimal('input_price_per_1k', 10, 6)->default(0);
            $table->decimal('output_price_per_1k', 10, 6)->default(0);
            $table->unsignedInteger('context_window')->nullable();
            $table->json('capabilities')->nullable(); // e.g. ["text","vision","tools"]
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
