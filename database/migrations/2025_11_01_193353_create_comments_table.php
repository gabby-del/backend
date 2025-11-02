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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            
            // Link to the request being commented on
            $table->foreignId('payment_request_id')->constrained()->onDelete('cascade');
            
            // Link to the user who wrote the comment (CEO, FM, FO, or HR)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // The content of the comment/feedback
            $table->text('content');
            
            // For tracking if the comment was a clarification request vs general remark
            $table->boolean('is_clarification_request')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};