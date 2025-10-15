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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("requester_id");
            $table->foreignId("department_id");
            $table->foreignId("project_id");
            $table->foreignId("cost_center_id");
            $table->string("title");
            $table->string("description");
            $table->decimal("amount");
            $table->string("vendor_name");
            $table->string("vendor_details");
            $table->string("expense_category");
            $table->string("status");
            $table->timestamp("submitted_at");
            $table->timestamp("approved_at");
            $table->timestamp("paid_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
