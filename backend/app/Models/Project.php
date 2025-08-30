<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'account_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget',
        'created_by',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
