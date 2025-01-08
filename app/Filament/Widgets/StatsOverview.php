<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::count()),
            Stat::make('Total Clasess', Classes::count()),
            Stat::make('Total Sections', Section::count()),
        ];
    }
}