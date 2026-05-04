<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTaxGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_group_id',
        'tax_group_name',
        'created_by',
    ];
}
