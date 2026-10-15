<?php

namespace App\Filament\Resources\PaymentDetailResource\Pages;

use App\Filament\Resources\PaymentDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreatePaymentDetail extends CreateRecord
{
    protected static string $resource = PaymentDetailResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        $data['payment_at'] = now();
        
        return $data;
    }
}
