<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSalesTaxGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_item_group',
        'tax_group_name',
        'created_by',
    ];
}
