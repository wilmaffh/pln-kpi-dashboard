<?php
// FILE: database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\JenisKpi;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. USERS ──────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@pln-garut.com'],
            [
                'name'     => 'Administrator PLN UP3 Garut',
                'username' => 'admin_pln',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staf@pln-garut.com'],
            [
                'name'     => 'Staf Komunikasi',
                'username' => 'staf_kominfo',
                'password' => Hash::make('staf123'),
                'role'     => 'user',
            ]
        );

        // ── 2. SEMESTER AKTIF ─────────────────────────────────────────────
        Semester::updateOrCreate(
            ['tahun' => 2025, 'periode' => 'JAN_JUN'],
            ['nama_semester' => 'Semester 1 Tahun 2025', 'is_active' => true]
        );
        Semester::updateOrCreate(
            ['tahun' => 2025, 'periode' => 'JUL_DES'],
            ['nama_semester' => 'Semester 2 Tahun 2025', 'is_active' => false]
        );

        // ── 3. JENIS KPI (5 default) ──────────────────────────────────────
        $kpis = [
            ['nama_kpi' => 'Publikasi Siaran Pers',          'kode_kpi' => 'siaran_pers',      'tipe_input' => 'detail_form', 'urutan' => 1],
            ['nama_kpi' => 'Publikasi Wajib',                'kode_kpi' => 'publikasi_wajib',  'tipe_input' => 'detail_form', 'urutan' => 2],
            ['nama_kpi' => 'Pengelolaan Akun Media Sosial',  'kode_kpi' => 'media_sosial',     'tipe_input' => 'detail_form', 'urutan' => 3],
            ['nama_kpi' => 'Pengelolaan Informasi Publik',   'kode_kpi' => 'informasi_publik', 'tipe_input' => 'detail_form', 'urutan' => 4],
            ['nama_kpi' => 'Tindaklanjut OFI to AFI',        'kode_kpi' => 'ofi_afi',          'tipe_input' => 'detail_form', 'urutan' => 5],
        ];

        foreach ($kpis as $kpi) {
            JenisKpi::updateOrCreate(
                ['kode_kpi' => $kpi['kode_kpi']],
                array_merge($kpi, ['is_default' => true, 'is_active' => true])
            );
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('   Admin : admin@pln-garut.com / admin123');
        $this->command->info('   User  : staf@pln-garut.com  / staf123');
    }
}