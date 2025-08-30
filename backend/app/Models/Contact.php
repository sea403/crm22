<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'account_id',
        'city',
        'country_code',
        'zipcode',
        'state',
        'name',
        'email',
        'phone',
        'company_name',
        'position',
        'address',
        'notes',
        'created_by',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
