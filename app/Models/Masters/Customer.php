<?php

namespace App\Models\Masters;

use App\Support\DataAreaId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'customer_id',
        'name',
        'address_flat',
        'address_building',
        'address_area',
        'address_city',
        'address_emirates',
        'address_pincode',
        'phone',
        'email',
        'created_by',
    ];

    public function setCompanyIdAttribute(mixed $value): void
    {
        $this->attributes['company_id'] = $value === null || $value === '' ? null : DataAreaId::normalize((string) $value);
    }
}
