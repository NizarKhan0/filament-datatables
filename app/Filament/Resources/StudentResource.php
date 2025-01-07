<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->unique(),
                // ->placeholder('Enter Student Name'),

                TextInput::make('email')
                    ->required()
                    ->unique(),
                // ->placeholder('Enter Student Email'),

                TextInput::make('phone_number')
                    ->required()
                    ->tel()
                    ->unique(),
                // ->placeholder('Enter Student Phone Number'),

                TextInput::make('address')
                    ->required(),

                Select::make('class_id')
                    ->relationship(name: 'class', titleAttribute: 'name')
                    ->reactive(),

                Select::make('section_id')
                    ->label('Select Section')
                    ->options(function (callable $get) {
                        $classId = $get('class_id');

                        if ($classId) {
                            return Section::where('class_id', $classId)->get()->pluck('name', 'id')->toArray();
                        }
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
