<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Exports\StudentsExport;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\StudentResource\Pages;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'Academic Management';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

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
                    }),
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
                Filter::make('class-section-filter')
                    ->form([
                        Select::make('class_id')
                            ->label('Filter by Class')
                            ->placeholder('Select Class')
                            ->options(
                                Classes::pluck('name', 'id')->toArray()
                            )
                            ->afterStateUpdated(
                                function (callable $set) {
                                    $set('section_id', null);
                                }
                            ),
                        Select::make('section_id')
                            ->label('Filter by Section')
                            ->placeholder('Select a Section')
                            ->options(
                                function (callable $get) {
                                    $classId = $get('class_id');

                                    if ($classId) {
                                        return Section::where('class_id', $classId)->get()->pluck('name', 'id')->toArray();
                                    }
                                }
                            ),
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['class_id'],
                                fn(Builder $query, $classId): Builder => $query->where('class_id', $classId),
                            )
                            ->when(
                                $data['section_id'],
                                fn(Builder $query, $sectionId): Builder => $query->where('section_id', $sectionId),
                            )
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
                Action::make('Downlaod Pdf')
                    // ->icon('heroicon-o-document-download')
                    // ->url(fn(Student $record): string => route('student.pdf.download', ['record' => $record]))
                    ->openUrlInNewTab(),

                Action::make('View Qr Code')
                    // ->icon('heroicon-o-document-download')
                    // ->url(fn(Student $record): string => static::getUrl('qr-code', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn(Collection $records) => (new StudentsExport($records))->download('students.xlsx')),
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

    public static function getNavigationBadge(): ?string
    {
        return self::$model::count();
    }
}
