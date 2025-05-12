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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('pr_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('department');
            $table->decimal('estimated_amount', 15, 2);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->string('document_path')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
