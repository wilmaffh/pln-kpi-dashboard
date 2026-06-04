<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class KpiSpreadsheetConfig extends Model
{
    protected $fillable = [
        'semester_id', 'url_spreadsheet', 'sheet_name', 'is_active',
    ];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}