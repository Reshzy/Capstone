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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abstract_of_quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->string('po_number')->unique();
            $table->date('po_date');
            $table->decimal('total_amount', 12, 2);
            $table->string('delivery_location');
            $table->integer('delivery_days');
            $table->enum('status', ['pending', 'delivered', 'cancelled'])->default('pending');
            $table->string('document_path')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
