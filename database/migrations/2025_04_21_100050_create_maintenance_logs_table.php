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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->date('maintenance_date');
            $table->text('issue_description');
            $table->decimal('product_cost', 10, 2)->nullable();
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->index(['property_id', 'unit_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
