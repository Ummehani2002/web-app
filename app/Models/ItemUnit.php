<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'unit_id',
        'unit_name',
        'definition',
        'created_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
