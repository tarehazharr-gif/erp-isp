<?php

namespace App\Filament\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
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

            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->preload()
                ->required(),

            // BARIS INI WAJIB ADA UNTUK MEMPERBAIKI ERROR DUE_DATE
            Forms\Components\DatePicker::make('due_date')
                ->label('Tanggal Jatuh Tempo')
                ->required()
                ->default(now()->addDays(7)), 

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

            // Tombol Cetak PDF yang kita buat tadi
    Tables\Actions\Action::make('pdf')
        ->label('Cetak PDF')
        ->color('info')
        ->icon('heroicon-o-arrow-down-tray')
        ->action(function (Invoice $record) {
            $pdf = Pdf::loadView('invoice-pdf', ['record' => $record]);
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, "Invoice-{$record->invoice_number}.pdf");
        }),
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
