<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupCourseAssignment extends Model
{
    protected $fillable = [
        'group_id',
        'subject_id',
        'instructor_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
