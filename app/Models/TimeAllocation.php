<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeAllocation extends Model
{
    // $guarded = [];
    protected $fillable = [
    'id',             // <- à ajouter
    'employeeId',
    'year',
    'agreement',
    'date',
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
}
