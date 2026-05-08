<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
{
    return [
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Pelanggan', \App\Models\Customer::count())
            ->description('Pelanggan terdaftar')
            ->descriptionIcon('heroicon-m-users')
            ->color('success'),
            
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Tagihan Belum Bayar', \App\Models\Invoice::where('status', 'unpaid')->count())
            ->description('Segera tagih!')
            ->descriptionIcon('heroicon-m-clock')
            ->color('danger'),

        \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Pendapatan', 'Rp ' . number_format(\App\Models\Invoice::where('status', 'paid')->sum('amount'), 0, ',', '.'))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('info'),
    ];
}
}
