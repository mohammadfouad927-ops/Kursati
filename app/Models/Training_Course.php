<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training_Course extends Model
{
        protected $table = 'training_courses';
        protected $fillable = [
            'name',
            'start_date',
            'end_date',
            'price',
            'description',
            'is_active',
        ];

        public function subjects()
        {
            return $this->belongsToMany(Subject::class, 'program_courses', 'training_course_id', 'subject_id');
        }
}
