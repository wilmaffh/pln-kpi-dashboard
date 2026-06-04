<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
 
class KpiSiaranPers extends Model implements HasMedia
{
    use InteractsWithMedia;
 
    protected $table    = 'kpi_siaran_pers';
    protected $fillable = [
        'submission_id', 'bulan', 'judul_draft',
        'teks_draft_release', 'tanggal_kirim', 'platform_pengiriman',
    ];
    protected $casts = ['tanggal_kirim' => 'date'];
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
 
    public function registerMediaCollections(): void
    {
        // Screenshot WA bukti pengiriman siaran pers
        $this->addMediaCollection('eviden_pengiriman')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
 
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)->height(200)->nonQueued();
    }
}
 
 