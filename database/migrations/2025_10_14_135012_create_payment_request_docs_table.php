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
        Schema::create('payment_request_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("payment_request_id");
            $table->string("file_name");
            $table->string("file_path");
            $table->integer("file_size");
            $table->timestamp("uploaded_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_request_docs');
    }
};
