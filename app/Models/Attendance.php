<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
    ];

    protected $casts = [
        'date' => 'date',        // obligatorio para whereDate y ->format('d/m/Y')
        // check_in y check_out son strings (hora), no necesitan cast de datetime
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}