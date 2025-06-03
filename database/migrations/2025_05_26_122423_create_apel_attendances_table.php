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
        Schema::create('apel_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apel_session_id')->constrained('apel_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('keterangan_id')->constrained('keterangans')->onDelete('cascade');
            // $table->foreignId('keterangan_id')->constrained('keterangans')->onDelete('set null');
            $table->foreignId('keterangan_id')->constrained('keterangans')->onDelete('restrict');
            $table->enum('status', ['draft', 'submitted', 'verified', 'done'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apel_attendances');
    }
};
