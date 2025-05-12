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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('rating', 3, 2)->nullable();
            $table->timestamps();
        });
        
        // Create pivot table for procurement categories
        Schema::create('procurement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Create pivot table for supplier categories
        Schema::create('procurement_category_supplier', function (Blueprint $table) {
            $table->foreignId('procurement_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->primary(['procurement_category_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_category_supplier');
        Schema::dropIfExists('procurement_categories');
        Schema::dropIfExists('suppliers');
    }
};
