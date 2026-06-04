<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class JenisKpi extends Model
{
    protected $fillable = [
        'nama_kpi', 'kode_kpi', 'tipe_input',
        'is_default', 'is_active', 'urutan',
    ];
 
    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];
 
    public function kpiSubmissions(): HasMany
    {
        return $this->hasMany(KpiSubmission::class);
    }
 
    // Map kode_kpi ke resource Filament yang menanganinya
    public static function getResourceMap(): array
    {
        return [
            'siaran_pers'      => \App\Filament\Resources\KpiSiaranPersResource::class,
            'publikasi_wajib'  => \App\Filament\Resources\KpiPublikasiWajibResource::class,
            'media_sosial'     => \App\Filament\Resources\KpiMediaSosialResource::class,
            'informasi_publik' => \App\Filament\Resources\KpiInformasiPublikResource::class,
            'ofi_afi'          => \App\Filament\Resources\KpiOfiAfiResource::class,
        ];
    }
}