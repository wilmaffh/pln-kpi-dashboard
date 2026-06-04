<?php
// ══════════════════════════════════════════════════════
// FILE: app/Models/KpiSubmission.php
// ══════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;
 
class KpiSubmission extends Model
{
    use SoftDeletes;
 
    protected $fillable = [
        'semester_id', 'jenis_kpi_id', 'user_id',
        'metode_input', 'status',
    ];
 
    public function semester(): BelongsTo  { return $this->belongsTo(Semester::class); }
    public function jenisKpi(): BelongsTo  { return $this->belongsTo(JenisKpi::class); }
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
 
    public function siaranPers(): HasOne   { return $this->hasOne(KpiSiaranPers::class, 'submission_id'); }
    public function publikasiWajib(): HasOne { return $this->hasOne(KpiPublikasiWajib::class, 'submission_id'); }
    public function mediaSosial(): HasMany { return $this->hasMany(KpiMediaSosial::class, 'submission_id'); }
    public function informasiPublik(): HasMany { return $this->hasMany(KpiInformasiPublik::class, 'submission_id'); }
    public function ofiAfi(): HasOne       { return $this->hasOne(KpiOfiAfi::class, 'submission_id'); }
    public function genericFiles(): HasMany { return $this->hasMany(KpiGenericFile::class, 'submission_id'); }
 
    public function submit(): void         { $this->update(['status' => 'submitted']); }
    public function isSubmitted(): bool    { return $this->status === 'submitted'; }
    public function isDraft(): bool        { return $this->status === 'draft'; }
    public function canEdit(): bool        { return $this->status === 'draft'; }
 
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'warning',
            'submitted' => 'success',
            default     => 'gray',
        };
    }
}
 
 