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
        Schema::create('pikets', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('pajaga_by')->constrained('users')->references('id')->onDelete('cascade');
            // $table->foreignId('bajaga_first_by')->constrained('users')->references('id')->onDelete('cascade');
            // $table->foreignId('bajaga_second_by')->constrained('users')->references('id')->onDelete('cascade');

            $table->foreignId('pajaga_by')->nullable()->constrained('users')->references('id')->onDelete('set null');
            $table->foreignId('bajaga_first_by')->nullable()->constrained('users')->references('id')->onDelete('set null');
            $table->foreignId('bajaga_second_by')->nullable()->constrained('users')->references('id')->onDelete('set null');
            $table->date('piket_date');
            // $table->foreignId('created_by')->constrained('users')->references('id')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->references('id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pikets');
    }
};
