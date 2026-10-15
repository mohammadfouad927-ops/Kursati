<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor, HasIcon
{
    case PENDING = 'pending';
    case PARTIALLY_PAID = 'partially_paid';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';
    case FAILED = 'failed';
    case REJECTED = 'rejected';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::COMPLETED => 'Completed',
            self::APPROVED => 'Approved',
            self::FAILED => 'Failed',
            self::REJECTED => 'Rejected',
            self::REFUNDED => 'Refunded',
            self::PARTIALLY_REFUNDED => 'Partially Refunded',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PARTIALLY_PAID => 'info',
            self::COMPLETED => 'success',
            self::APPROVED => 'approved',
            self::FAILED => 'danger',
            self::REJECTED => 'failed',
            self::REFUNDED => 'gray',
            self::PARTIALLY_REFUNDED => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PARTIALLY_PAID => 'heroicon-o-chart-pie',
            self::COMPLETED, self::APPROVED => 'heroicon-o-check-circle',
            self::FAILED, self::REJECTED => 'heroicon-o-x-circle',
            self::REFUNDED => 'heroicon-o-arrow-path',
            self::PARTIALLY_REFUNDED => 'heroicon-o-arrow-uturn-left',
        };
    }
}
