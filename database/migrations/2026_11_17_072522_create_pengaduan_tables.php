<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_kategori_id')->constrained('pengaduan_kategoris')->cascadeOnDelete();
            $table->string('nomor')->unique();
            $table->string('nama');
            $table->string('no_hp', 30);
            $table->text('isi');
            $table->string('status', 20)->default('baru');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
        });

        Schema::create('pengaduan_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduans')->cascadeOnDelete();
            $table->string('sender', 10);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index(['pengaduan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_feedbacks');
        Schema::dropIfExists('pengaduans');
        Schema::dropIfExists('pengaduan_kategoris');
    }
};
