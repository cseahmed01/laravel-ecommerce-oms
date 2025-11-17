<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'row_number',
        'status',
        'message',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(ProductImport::class, 'import_id');
    }
}