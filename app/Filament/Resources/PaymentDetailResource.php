<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentDetailResource\Pages;
use App\Filament\Resources\PaymentDetailResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\PaymentDetail;
use Filament\Forms;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentDetailResource extends Resource
{
    protected static ?string $model = PaymentDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Basic Information')
                ->description('The Information About The Student, The Coruse And The Amount')
                ->schema([
                    Forms\Components\Select::make('enrollment_id')
                        ->required()
                        ->label('Student')
                        ->options(
                            fn (): array => Enrollment::with('student:id,name')
                                ->whereNot('payment_status','=',PaymentStatus::COMPLETED)
                                ->latest()
                                ->limit(10)
                                ->get(['id', 'student_id'])
                                ->mapWithKeys(fn ($enrollment) => [
                                    $enrollment->id => $enrollment->student?->name ?? 'Unknown',
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->getSearchResultsUsing(fn($search) => 
                           fn (string $search): array => Enrollment::with('student:id,name')
                                ->whereHas('student', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                                ->latest()
                                ->limit(10)
                                ->get(['id', 'student_id'])
                                ->mapWithKeys(fn ($enrollment) => [
                                    $enrollment->id => $enrollment->student?->name ?? 'Unknown',
                                ])
                                ->toArray()
                        )
                        ->getOptionLabelUsing(fn ($value): ?string => ($enrollment = Enrollment::with('student:id,name')->find($value))
                                ? $enrollment->student?->name
                                : null),
                    Forms\Components\TextInput::make('amount')
                        ->required()
                        ->numeric(),
                ])->columns(2),
                Forms\Components\Section::make('Payment Method')
                ->description('Select The Method To Pay')
                ->schema([
                    Forms\Components\Select::make('payment_method')
                        ->options(PaymentMethod::class)
                        ->live()
                        ->required(),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Phone Number')
                        ->prefix('+20')
                        ->tel()
                        ->telRegex('/^(?:\+20|0)?1[0125]\d{8}$/')
                        ->visible(fn(Get $get):bool => PaymentMethod::tryFrom($get('payment_method')?? '')?->requiresTransferDetails() ?? false)
                        ->required(fn (Get $get): bool => PaymentMethod::tryFrom($get('payment_method') ?? '')?->requiresTransferDetails() ?? false),
                    Forms\Components\TextInput::make('reference_number')
                        ->maxLength(255)
                        ->visible(fn(Get $get): bool => PaymentMethod::tryFrom($get('payment_method')?? '')?->requiresTransferDetails() ?? false)
                        ->required(fn (Get $get): bool => PaymentMethod::tryFrom($get('payment_method') ?? '')?->requiresTransferDetails() ?? false),
                    Forms\Components\FileUpload::make('proof_screenshot')
                        ->image()
                        ->visible(fn(Get $get): bool => PaymentMethod::tryFrom($get('payment_method')?? '')?->requiresTransferDetails() ?? false)
                        ->maxSize(2024)
                        ->openable()
                        ->required(fn (Get $get): bool => PaymentMethod::tryFrom($get('payment_method') ?? '')?->requiresTransferDetails() ?? false),
                ])->columns(2),
                // Forms\Components\DateTimePicker::make('paid_at')
                //     ->required()
                //     ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->numeric()
                ->sortable(),

                Tables\Columns\TextColumn::make('enrollment.student.name')
                    ->label('Student')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime('d M Y - h:i A')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->placeholder('Not Verified Yet'),

                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime('d M Y - h:i A')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    PaymentStatus::APPROVED->value => PaymentStatus::APPROVED->getLabel(),
                    PaymentStatus::FAILED->value => PaymentStatus::FAILED->getLabel(),
                    PaymentStatus::PENDING->value => PaymentStatus::PENDING->getLabel(),
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn($record): bool => $record->status != PaymentStatus::PENDING),
                Tables\Actions\DeleteAction::make()
                ->visible(fn($record): bool => $record->status != PaymentStatus::PENDING),
                Tables\Actions\Action::make('verify')
                    ->label('Verify Payment')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning') // Yellow button
                    ->visible(fn (PaymentDetail $record): bool => $record->status === PaymentStatus::PENDING)
                    ->modalHeading('Verify Payment Request')
                    ->modalDescription('Review the payment details and transfer proof submitted by the student before taking action.')
                    ->modalFooterActionsAlignment(Alignment::End)
                    // 1. Modal Body: Read-only display of amount, reference, and screenshot
                    ->infolist([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Amount Paid')
                                    ->money('EGP'),

                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label('Payment Method'),

                                Infolists\Components\TextEntry::make('phone_number')
                                    ->label('Sender Phone Number')
                                    ->default('N/A'),

                                Infolists\Components\TextEntry::make('reference_number')
                                    ->label('Reference Number')
                                    ->default('N/A'),
                            ]),

                        Infolists\Components\Section::make('Transfer Proof Screenshot')
                            ->schema([
                                Infolists\Components\ImageEntry::make('proof_screenshot')
                                    ->label('')
                                    ->height(250)
                                    ->placeholder('No proof screenshot uploaded.'),
                            ]),
                    ])

                    // 2. Custom Modal Footer Actions: Approve (Success) or Reject (Danger)
                    ->modalActions([
                        // APPROVE BUTTON
                        Tables\Actions\Action::make('approve_payment')
                            ->label('Approve Payment')
                            ->color('success')
                            ->icon('heroicon-o-check-circle')
                            ->action(function (PaymentDetail $record): void {
                                // Update payment record
                                $record->update([
                                    'status' => PaymentStatus::APPROVED, // or 'approved'
                                    'verified_by' => Auth::id(),
                                    'verified_at' => now(),
                                    'paid_at' => $record->paid_at ?? now(),
                                ]);

                                // Update parent enrollment
                                if ($enrollment = $record->enrollment) {
                                    $totalVerifiedPaid = $enrollment->paymentDetails()
                                        ->where('status', PaymentStatus::APPROVED->value)
                                        ->sum('amount');

                                    $coursePrice = $enrollment->group->trainingCourse->price ?? 0;

                                    if ($totalVerifiedPaid >= $coursePrice) {
                                        $enrollment->update(['payment_status' => PaymentStatus::COMPLETED]);
                                    } elseif ($totalVerifiedPaid > 0) {
                                        $enrollment->update(['payment_status' => PaymentStatus::PARTIALLY_PAID]);
                                    }
                                }



                                Notification::make()
                                    ->title('Payment Approved Successfully')
                                    ->success()
                                    ->send();
                            })
                            ->cancelParentActions(),

                        // REJECT BUTTON
                        Tables\Actions\Action::make('reject_payment')
                            ->label('Reject Payment')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                            ->action(function (PaymentDetail $record, Tables\Actions\Action $action): void {
                                $record->update([
                                    'status' => PaymentStatus::FAILED, // or 'failed'
                                    'verified_by' => Auth::id(),
                                    'verified_at' => now(),
                                ]);

                                Notification::make()
                                    ->title('Payment Rejected')
                                    ->warning()
                                    ->send();
                            })
                            ->cancelParentActions(),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);     
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentDetails::route('/'),
            'create' => Pages\CreatePaymentDetail::route('/create'),
            'edit' => Pages\EditPaymentDetail::route('/{record}/edit'),
        ];
    }
}
