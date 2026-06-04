<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
 
class KpiPublikasiWajib extends Model
{
    protected $table    = 'kpi_publikasi_wajib';
    protected $fillable = [
        'submission_id', 'bulan', 'tahun',
        'nomor_surat', 'nama_konten', 'unit',
    ];
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
 
    public function links(): HasMany
    {
        return $this->hasMany(PublikasiWajibLink::class, 'publikasi_wajib_id');
    }
}