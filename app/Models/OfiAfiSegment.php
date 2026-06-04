<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
 
class OfiAfiSegment extends Model implements HasMedia
{
    use InteractsWithMedia;
 
    protected $table    = 'ofi_afi_segments';
    protected $fillable = [
        'ofi_afi_id', 'nama_segment', 'label_segment',
        'kelompok', 'jumlah_file', 'is_complete', 'keterangan',
    ];
    protected $casts = ['is_complete' => 'boolean'];
 
    public function ofiAfi(): BelongsTo
    {
        return $this->belongsTo(KpiOfiAfi::class, 'ofi_afi_id');
    }
 
    public function registerMediaCollections(): void
    {
        // Collection name = nama_segment untuk grouping otomatis
        $this->addMediaCollection($this->nama_segment ?? 'evidence')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }
}