<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'file_path',
        'status',
        'total_rows',
        'processed_rows',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProductImportLog::class, 'import_id');
    }
}