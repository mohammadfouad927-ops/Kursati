<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'googleMapLink',
        'phone_number',
        'address',
        'is_active',
        'email',
    ];

}
