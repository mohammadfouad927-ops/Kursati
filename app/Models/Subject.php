<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function trainingCourses()
    {
        return $this->belongsToMany(Training_Course::class, 'program_courses', 'subject_id', 'training_course_id')->withPivot('');
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'instructor_qualifications', 'subject_id', 'instructor_id');
    }
}
