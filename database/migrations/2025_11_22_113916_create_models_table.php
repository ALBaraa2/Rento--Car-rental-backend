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
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('year')->nullable();
            $table->enum('type', ['باص', 'سيارة', 'دراجة نارية'])->default('سيارة');
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('seats')->nullable();
            $table->string('doors')->nullable();
            $table->string('transmission')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
