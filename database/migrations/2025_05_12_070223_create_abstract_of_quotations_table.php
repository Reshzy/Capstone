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
        Schema::create('abstract_of_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_for_quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('awarded_supplier_id')->nullable()->constrained('suppliers');
            $table->string('aoq_number')->unique();
            $table->date('aoq_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['draft', 'for_approval', 'approved', 'rejected'])->default('draft');
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
        Schema::dropIfExists('abstract_of_quotations');
    }
};
