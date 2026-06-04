<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
 
class KpiOfiAfi extends Model
{
    protected $table    = 'kpi_ofi_afi';
    protected $fillable = ['submission_id'];
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
 
    public function segments(): HasMany
    {
        return $this->hasMany(OfiAfiSegment::class, 'ofi_afi_id');
    }
 
    public static function getAllSegments(): array
    {
        return [
            ['nama' => 'mup3',            'label' => 'MUP3',                        'kelompok' => 'mup3'],
            ['nama' => 'asman_jar',       'label' => 'Asman Jaringan',              'kelompok' => 'asman'],
            ['nama' => 'asman_transener', 'label' => 'Asman Transaksi Energi',      'kelompok' => 'asman'],
            ['nama' => 'asman_ren',       'label' => 'Asman Rencana',               'kelompok' => 'asman'],
            ['nama' => 'asman_niaga',     'label' => 'Asman Niaga',                 'kelompok' => 'asman'],
            ['nama' => 'asman_keu',       'label' => 'Asman Keuangan',              'kelompok' => 'asman'],
            ['nama' => 'asman_kons',      'label' => 'Asman Konstruksi',            'kelompok' => 'asman'],
            ['nama' => 'mulp_garut_kota', 'label' => 'MULP Garut Kota',             'kelompok' => 'mulp'],
            ['nama' => 'mulp_cikajang',   'label' => 'MULP Cikajang',               'kelompok' => 'mulp'],
            ['nama' => 'mulp_leles',      'label' => 'MULP Leles',                  'kelompok' => 'mulp'],
            ['nama' => 'mulp_cibatu',     'label' => 'MULP Cibatu',                 'kelompok' => 'mulp'],
            ['nama' => 'mulp_pameungpeuk','label' => 'MULP Pameungpeuk',            'kelompok' => 'mulp'],
            ['nama' => 'tl_adum',         'label' => 'Team Leader Adum',            'kelompok' => 'tl_adum'],
            ['nama' => 'konten_wajib_ig', 'label' => 'Konten Wajib Medsos (IG)',    'kelompok' => 'medsos'],
            ['nama' => 'wag_internal',    'label' => 'WAG Internal',                'kelompok' => 'wag'],
            ['nama' => 'wag_eksternal',   'label' => 'WAG Eksternal',               'kelompok' => 'wag'],
        ];
    }
}
 
 