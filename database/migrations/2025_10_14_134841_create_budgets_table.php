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
            $table->foreignId("department_id");
            $table->foreignId("project_id");
            $table->string("budget_type");
            $table->string("fiscal_year");
            $table->decimal("total_amount");
            $table->decimal("allocated_amount");
            $table->decimal("spent_amount");
            $table->decimal("available_amount");
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
