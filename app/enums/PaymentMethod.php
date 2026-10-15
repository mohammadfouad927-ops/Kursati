<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel, HasColor, HasIcon
{
    case CASH = 'cash';
    case INSTAPAY = 'instapay';
    case ELECTRONIC_WALLET = 'electronic_wallet';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::INSTAPAY => 'InstaPay',
            self::ELECTRONIC_WALLET => 'Electronic Wallet',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CASH => 'success',
            self::INSTAPAY => 'info',
            self::ELECTRONIC_WALLET => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CASH => 'heroicon-o-banknotes',
            self::INSTAPAY => 'heroicon-o-bolt',
            self::ELECTRONIC_WALLET => 'heroicon-o-device-phone-mobile',
        };
    }

    /**
     * Determines if the payment method requires transfer details.
     */
    public function requiresTransferDetails(): bool
    {
        return match ($this) {
            self::INSTAPAY, self::ELECTRONIC_WALLET => true,
            self::CASH => false,
        };
    }
}