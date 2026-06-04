<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class KpiGenericFile extends Model
{
    protected $table    = 'kpi_generic_files';
    protected $fillable = [
        'submission_id', 'file_name', 'file_path', 'file_type', 'file_size',
    ];
 
    public function submission(): BelongsTo
    {
        return $this->belongsTo(KpiSubmission::class, 'submission_id');
    }
}