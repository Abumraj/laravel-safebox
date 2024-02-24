<?php

namespace App\Filament\Widgets;

use App\Models\file;
use App\Models\Product;
use App\Models\StarredFile;
use App\Models\subscriptionplan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All Users', User::query()->count())
            ->description('All safeBox users')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])

            ->color('info'),
        Stat::make('Products', Product::query()->count())
            ->description('All back up products')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            // ->chart([7, 2, 10, 3, 15, 4, 17])

            ->color('danger'),
        Stat::make('Subscription Plans', subscriptionplan::query()->count())
            ->description('Available Plans')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])

            ->color('success'),
        Stat::make('All File', file::where('is_folder', 0)->count())
            ->description('All user backed up files')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])

            ->color('success'),

            Stat::make('Starred files', StarredFile::query()->count())
            ->description('All Starred files')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            Stat::make('Transactions', StarredFile::query()->count())
            ->description('All Transactions')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
        ];
    }
}
