<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trainingCourseAssiginment extends Model
{
    protected $fillable =[
        'instructor_id',
        'program_course_id',
        'group_id',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function programCourse()
    {
        return $this->belongsTo(ProgramCourse::class, 'program_course_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
