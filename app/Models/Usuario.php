<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
       use HasApiTokens,HasFactory,Notifiable;
    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password','rol'];

    protected $hidden= ['password','remember_token',];

    // Un usuario tiene muchos préstamos
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'id_usuario');
    }
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }




}

