<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;


class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'group_id',
        'payment_status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function paymentDetails(){
        return $this->hasMany(PaymentDetail::class);
    }

    protected $casts = [
        'payment_status' => PaymentStatus::class,
    ];
}
