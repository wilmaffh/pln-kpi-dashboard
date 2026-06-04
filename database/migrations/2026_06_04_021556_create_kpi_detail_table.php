<?php
// FILE: database/migrations/2025_01_01_000004_create_kpi_detail_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── KPI 1: SIARAN PERS ───────────────────────────────────────────
        // File eviden (screenshot WA) → Spatie Media Library
        // collection: 'eviden_pengiriman'
        Schema::create('kpi_siaran_pers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->unique()                          // 1 submission = 1 siaran pers header
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->tinyInteger('bulan')->unsigned()->comment('1-12');
            $table->string('judul_draft');
            $table->longText('teks_draft_release');
            $table->date('tanggal_kirim')->nullable();
            $table->string('platform_pengiriman')->default('WhatsApp');
            $table->timestamps();
        });

        // ── KPI 2: PUBLIKASI WAJIB ───────────────────────────────────────
        Schema::create('kpi_publikasi_wajib', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->unique()
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->tinyInteger('bulan')->unsigned();
            $table->year('tahun');
            $table->string('nomor_surat')->nullable();
            $table->string('nama_konten');
            $table->string('unit')->default('UP3 Garut');
            $table->timestamps();
        });

        // Sub-tabel: link per platform untuk publikasi wajib
        Schema::create('publikasi_wajib_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publikasi_wajib_id')
                ->constrained('kpi_publikasi_wajib')
                ->cascadeOnDelete();
            $table->enum('platform', [
                'instagram', 'twitter', 'facebook', 'tiktok', 'youtube'
            ]);
            $table->text('url');
            $table->timestamps();

            $table->unique(['publikasi_wajib_id', 'platform'], 'uq_pub_platform');
        });

        // ── KPI 3: MEDIA SOSIAL ──────────────────────────────────────────
        // status_sync: otomatisasi scraping metadata dari URL postingan
        Schema::create('kpi_media_sosial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->text('url_post');
            $table->date('tanggal_post')->nullable();
            $table->longText('caption')->nullable();
            $table->string('username_akun')->nullable();
            $table->string('platform')->nullable();     // instagram, twitter, dll
            $table->string('kategori_agenda_setting')
                ->default('Grand Theme');               // READ-ONLY untuk user
            $table->integer('skor_kpi')->nullable();
            $table->enum('status_sync', ['pending', 'success', 'failed'])
                ->default('pending');
            $table->text('sync_error')->nullable();     // pesan error jika gagal
            $table->timestamp('synced_at')->nullable(); // kapan terakhir di-sync
            $table->timestamps();

            $table->index(['submission_id', 'status_sync']);
        });

        // ── KPI 4: INFORMASI PUBLIK ──────────────────────────────────────
        Schema::create('kpi_informasi_publik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->date('tanggal_masuk');
            $table->string('sumber');
            $table->string('nomor_surat')->nullable();
            $table->text('perihal');
            $table->string('kategori_pemohon');
            $table->string('pengirim');
            $table->text('link_surat_jawaban')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->date('tanggal_balasan')->nullable();
            // AUTO-CALCULATED via Model observer: tanggal_balasan - tanggal_masuk
            $table->integer('hari_tindak_lanjut')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'tanggal_masuk']);
        });

        // ── KPI 5: OFI to AFI ────────────────────────────────────────────
        // Evidence per segmen → Spatie Media Library
        // collection: nama segmen (misal 'mup3', 'asman_jar', dll)
        Schema::create('kpi_ofi_afi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->unique()
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // Sub-tabel: segmen-segmen OFI AFI
        Schema::create('ofi_afi_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ofi_afi_id')
                ->constrained('kpi_ofi_afi')
                ->cascadeOnDelete();
            $table->string('nama_segment');   // 'mup3', 'asman_jar', dst
            $table->string('label_segment'); // 'MUP3', 'Asman Jaringan', dst
            $table->string('kelompok');       // 'mup3', 'asman', 'mulp', dll
            $table->integer('jumlah_file')->default(0);
            $table->boolean('is_complete')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['ofi_afi_id', 'nama_segment'], 'uq_segment');
            // File evidence → Spatie Media Library per record ini
            // $segment->addMedia($file)->toMediaCollection($segment->nama_segment)
        });

        // ── KPI DINAMIS: GENERIC FILES ───────────────────────────────────
        // Untuk KPI tambahan yang dibuat Admin (file_only mode)
        Schema::create('kpi_generic_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->constrained('kpi_submissions')
                ->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, xlsx, zip, image
            $table->bigInteger('file_size')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_generic_files');
        Schema::dropIfExists('ofi_afi_segments');
        Schema::dropIfExists('kpi_ofi_afi');
        Schema::dropIfExists('kpi_informasi_publik');
        Schema::dropIfExists('kpi_media_sosial');
        Schema::dropIfExists('publikasi_wajib_links');
        Schema::dropIfExists('kpi_publikasi_wajib');
        Schema::dropIfExists('kpi_siaran_pers');
    }
};