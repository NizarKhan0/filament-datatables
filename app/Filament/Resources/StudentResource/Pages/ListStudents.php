<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Imports\StudentsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\StudentResource;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importStudents')
                ->label('Import Students')
                ->color('danger')
                ->form([
                    FileUpload::make('attachment'),
                ])
                ->action(function (array $data) {
                    $file = public_path("storage/" . $data['attachment']);

                    // dd($file);
                    Excel::import(new StudentsImport, $file);

                    Notification::make()
                        ->success()
                        ->title('Students Imported Successfully')
                        ->body('All students have been imported successfully.')
                        ->send();
                }),
        ];
    }
}
