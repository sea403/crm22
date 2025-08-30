<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'website',
        'phone',
        'industry',
        'billing_address',
        'shipping_address',
        'city',
        'state',
        'country_code',
        'zipcode',
        'notes',
        'created_by',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
