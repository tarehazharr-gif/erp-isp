<?php

namespace App\Filament\Resources;

use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('invoice_number')
                ->default('INV-' . date('Ymd-His'))
                ->readonly()
                ->required(),

            // Bagian ini yang memperbaiki error "customer_id doesn't have a default value"
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name') // Menarik nama dari tabel Customers
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'unpaid' => 'Belum Bayar',
                    'paid' => 'Lunas',
                ])
                ->default('unpaid')
                ->required(),
        ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('invoice_number')->searchable(),
            Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan'),
            Tables\Columns\TextColumn::make('amount')->money('IDR'),
            // Kolom Status dengan Warna (Badge)
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'unpaid' => 'danger',
                    default => 'gray',
                }),
        ])
        ->actions([
            // Tombol klik cepat untuk melunasi
            Tables\Actions\Action::make('setPaid')
                ->label('Set Lunas')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->action(fn ($record) => $record->update(['status' => 'paid']))
                ->visible(fn ($record) => $record->status === 'unpaid'),
            Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
