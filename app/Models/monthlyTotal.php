<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class monthlyTotal extends Model
{
        protected $fillable = [
        'id',             // <- à ajouter
        'employeeId',
        'date',
        'year',
        'bus',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'total',
    ];

    protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->date = now()->toDateString();
            });
    }
}
