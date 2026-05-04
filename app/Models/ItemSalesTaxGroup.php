<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSalesTaxGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'tax_item_group',
        'tax_group_name',
        'created_by',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
