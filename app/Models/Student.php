<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'students';

    protected $fillable =[
        'name',
        'email',
        'email_verified_at',
        'date_of_birth',
        'gender',
        'id_card_number',
        'phone_number',
        'address',
        'profile_picture',
        'city',
        'school',
        'grade',
    ];

    protected $hidden = [
        'id_card_number'
    ];
}
