<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone_number',
        'address',
        'profile_picture',
        'city',
        'qualification',
        'national_id',
        'salary',
        'expertise',
        'expert_years',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'instructor_qualifications', 'instructor_id', 'subject_id');
    }
}
