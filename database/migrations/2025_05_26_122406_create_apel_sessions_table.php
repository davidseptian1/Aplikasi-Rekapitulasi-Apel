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
        Schema::create('apel_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['pagi', 'sore']);
            $table->foreignId('subdis_id')->constrained('subdis')->onDelete('cascade');
            // $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apel_sessions');
    }
};
