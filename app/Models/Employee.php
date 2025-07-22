<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guard=[];
    protected $primaryKey = 'employeeId';

    public function timeAllocations()
    {
    return $this->hasMany(TimeAllocation::class, 'employeeId', 'employeeId');
    }

   public function supervisor()
{
    return $this->belongsTo(Employee::class, 'supervisorId', 'employeeId');
}
}
