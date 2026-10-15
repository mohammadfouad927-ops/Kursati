<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    protected $fillable = [
        'subject_id',
        'training_course_id',
    ];

    public function trainingCourse()
    {
        return $this->belongsTo(Training_Course::class, 'training_course_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function trainingCourseAssiginments(){
        return $this->hasMany(trainingCourseAssiginment::class);
    }
}
