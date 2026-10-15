<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSchdule extends Model
{
    protected $fillable =[
        'group_id',
        'day',
        'start_time',
        'end_time'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function casts()
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }
}
