<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pelanggan', Customer::count())
                ->description('Total pelanggan terdaftar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Invoice Unpaid', Invoice::where('status', 'unpaid')->count())
                ->description('Perlu ditagih')
                ->color('danger'),
            Stat::make('Total Pendapatan', 'Rp ' . number_format(Invoice::where('status', 'paid')->sum('amount'), 0, ',', '.'))
                ->description('Dari invoice lunas')
                ->color('success'),
        ];
    }
}