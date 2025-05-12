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
        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_for_quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number')->unique();
            $table->date('quotation_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'received', 'evaluated', 'awarded', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('is_awarded')->default(false);
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
    }
};
