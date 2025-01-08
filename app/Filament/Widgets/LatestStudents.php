<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Student;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStudents extends BaseWidget
{
    //ini utk define widget tu nak letak mana atas/bawah
    public static ?int $sort = 2;

    //ini utk column table
    protected int | string |array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table

            ->query(
                Student::query()
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)

            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone_number')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('class.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('section.name')
                    ->sortable()
                    ->searchable(),
            ]);
    }
}
