<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable =
    [
        'name',
        'training_course_id',
        'branch_id',
        'capacity',
        'is_active',
    ];

    public function trainingCourse()
    {
        return $this->belongsTo(Training_Course::class, 'training_course_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function groupSchudles(){
        return $this->hasMany(GroupSchdule::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
