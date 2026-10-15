<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Attribute;
use DateTime;
use Dom\Attr;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    Protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_method',
        'phone_number',
        'reference_number',
        'proof_screenshot',
        'status',
        'verified_by',
        'verified_at',
        'paid_at',
    ];

    public function enrollment(){
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function verifier(){
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected $attributes = [
        'status' => \App\Enums\PaymentStatus::PENDING->value,
    ];

    protected $casts = [
        'payment_method' => \App\Enums\PaymentMethod::class,
        'status' => \APP\Enums\PaymentStatus::class,
    ];
}
