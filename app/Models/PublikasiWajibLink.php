<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class PublikasiWajibLink extends Model
{
    protected $table    = 'publikasi_wajib_links';
    protected $fillable = ['publikasi_wajib_id', 'platform', 'url'];
 
    public function publikasiWajib(): BelongsTo
    {
        return $this->belongsTo(KpiPublikasiWajib::class, 'publikasi_wajib_id');
    }
}