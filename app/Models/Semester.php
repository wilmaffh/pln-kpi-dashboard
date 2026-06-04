<?php
// ══════════════════════════════════════════════════════
// FILE: app/Models/Semester.php
// ══════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class Semester extends Model
{
    protected $fillable = ['nama_semester', 'tahun', 'periode', 'is_active'];
    protected $casts    = ['is_active' => 'boolean', 'tahun' => 'integer'];
 
    public function kpiSubmissions(): HasMany
    {
        return $this->hasMany(KpiSubmission::class);
    }
 
    public function spreadsheetConfigs(): HasMany
    {
        return $this->hasMany(KpiSpreadsheetConfig::class);
    }
 
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
 
    public function activate(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
 
    public function getPeriodeLabelAttribute(): string
    {
        return $this->periode === 'JAN_JUN' ? 'Januari - Juni' : 'Juli - Desember';
    }
}