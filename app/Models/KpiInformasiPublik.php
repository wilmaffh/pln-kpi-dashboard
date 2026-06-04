<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class KpiInformasiPublik extends Model
{
    protected $table    = 'kpi_informasi_publik';
    protected $fillable = [
        'submission_id', 'tanggal_masuk', 'sumber', 'nomor_surat',
        'perihal', 'kategori_pemohon', 'pengirim', 'link_surat_jawaban',
        'tindak_lanjut', 'tanggal_balasan', 'hari_tindak_lanjut',
    ];
    protected $casts = [
        'tanggal_masuk'   => 'date',
        'tanggal_balasan' => 'date',
    ];
 
    // Auto-hitung hari_tindak_lanjut setiap save
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->tanggal_masuk && $model->tanggal_balasan) {
                $model->hari_tindak_lanjut = $model->tanggal_masuk
                    ->diffInDays($model->tanggal_balasan);
            }
        });
    }
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
 
    public static function getKategoriOptions(): array
    {
        return [
            'Perorangan'        => 'Perorangan',
            'Badan Hukum'       => 'Badan Hukum',
            'Kelompok Orang'    => 'Kelompok Orang',
            'LSM / NGO'         => 'LSM / NGO',
            'Media / Wartawan'  => 'Media / Wartawan',
            'Mahasiswa'         => 'Mahasiswa',
            'Instansi Pemerintah' => 'Instansi Pemerintah',
            'Lainnya'           => 'Lainnya',
        ];
    }
}
 