<?php
// FILE: database/migrations/2025_01_01_000002_create_master_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── TABEL SEMESTERS ──────────────────────────────────────────────
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('nama_semester');         // contoh: "Semester 1 2025"
            $table->year('tahun');
            $table->enum('periode', ['JAN_JUN', 'JUL_DES']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['tahun', 'periode']);    // 1 periode per tahun
        });

        // ── TABEL JENIS_KPIS ─────────────────────────────────────────────
        Schema::create('jenis_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kpi');              // "Publikasi Siaran Pers"
            $table->string('kode_kpi')->unique();    // "siaran_pers"
            $table->enum('tipe_input', ['detail_form', 'file_only']);
            $table->boolean('is_default')->default(false); // 5 KPI bawaan = true
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ── TABEL KPI_SPREADSHEET_CONFIGS ────────────────────────────────
        // Untuk KPI Media Sosial - konfigurasi Google Sheet / Excel template UID
        Schema::create('kpi_spreadsheet_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnDelete();
            $table->text('url_spreadsheet')->nullable();
            $table->string('sheet_name')->default('Garut'); // nama sheet yg dibaca
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_spreadsheet_configs');
        Schema::dropIfExists('jenis_kpis');
        Schema::dropIfExists('semesters');
    }
};