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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            
            // 🟢 Link to the structural entity
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            
            
            // Financial tracking fields
            $table->decimal('amount_allocated', 15, 2);
            $table->decimal('amount_spent', 15, 2)->default(0);

            // 🟢 ADDED: Status for the workflow (Pending/Active/Archived)
            $table->enum('status', ['Draft', 'Pending', 'Active', 'Archived'])->default('Draft');
            
            // Categorization
            $table->enum('category', ['OPEX', 'CAPEX', 'PROJECT']);
            
            $table->year('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};