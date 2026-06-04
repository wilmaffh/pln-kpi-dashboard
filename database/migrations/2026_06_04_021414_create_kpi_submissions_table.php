<?php
// FILE: database/migrations/2025_01_01_000003_create_kpi_submissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->restrictOnDelete();
            $table->foreignId('jenis_kpi_id')
                ->constrained('jenis_kpis')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->enum('metode_input', ['detail_form', 'file_upload'])
                ->default('detail_form');
            $table->enum('status', ['draft', 'submitted'])
                ->default('draft');
            $table->timestamps();
            $table->softDeletes();

            // 1 user hanya boleh punya 1 submission per KPI per semester
            $table->unique(['semester_id', 'jenis_kpi_id', 'user_id'], 'uq_submission');

            // Index performa
            $table->index(['semester_id', 'status']);
            $table->index(['user_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_submissions');
    }
};