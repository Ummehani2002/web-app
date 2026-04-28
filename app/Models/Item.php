<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'd365_item_id',
        'item_name',
        'type',
        'item_category_id',
    ];
}
