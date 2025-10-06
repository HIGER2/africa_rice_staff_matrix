<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded  = [];
    protected $primaryKey = 'employeeId';
    public $timestamps = false; // ⚠️ désactive created_at / updated_at

    public function timeAllocations()
    {
        return $this->hasMany(TimeAllocation::class, 'employeeId', 'employeeId');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisorId', 'employeeId');
    }

    public function monthlyTotal()
    {
        return $this->belongsTo(monthlyTotal::class, 'employeeId', 'employeeId');
    }
}
