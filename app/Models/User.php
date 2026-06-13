<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con respuestas de seguridad
    public function securityAnswers()
    {
        return $this->hasMany(UserSecurityAnswer::class);
    }

    // Relación con empleado (uno a uno)
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // Relación con despliegues supervisados
    public function supervisedDeployments()
    {
        return $this->hasMany(Deployment::class, 'supervisor_id');
    }
}