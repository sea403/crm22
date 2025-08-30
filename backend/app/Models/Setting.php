<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
     protected $fillable = [
        'user_id',
        'default_currency',
        'currency_format',
        'number_format',
        'default_country',
        'timezone',
        'start_day_of_week',
        'date_format',
        'time_format',
        'fiscal_year_start',
        'default_language',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
