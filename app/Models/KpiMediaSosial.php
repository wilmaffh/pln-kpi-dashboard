<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class KpiMediaSosial extends Model
{
    protected $table    = 'kpi_media_sosial';
    protected $fillable = [
        'submission_id', 'url_post', 'tanggal_post', 'caption',
        'username_akun', 'platform', 'kategori_agenda_setting',
        'skor_kpi', 'status_sync', 'sync_error', 'synced_at',
    ];
    protected $casts = [
        'tanggal_post' => 'date',
        'synced_at'    => 'datetime',
    ];
 
    protected $attributes = ['kategori_agenda_setting' => 'Grand Theme'];
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
 
    public function getSyncStatusColorAttribute(): string
    {
        return match($this->status_sync) {
            'success' => 'success',
            'failed'  => 'danger',
            default   => 'warning',
        };
    }
 
    // Deteksi platform dari URL
    public function detectPlatform(): string
    {
        $url = strtolower($this->url_post ?? '');
        if (str_contains($url, 'instagram'))  return 'instagram';
        if (str_contains($url, 'twitter') || str_contains($url, 'x.com')) return 'twitter';
        if (str_contains($url, 'facebook'))   return 'facebook';
        if (str_contains($url, 'tiktok'))     return 'tiktok';
        if (str_contains($url, 'youtube'))    return 'youtube';
        return 'lainnya';
    }
}
 