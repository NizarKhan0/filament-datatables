<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class StudentsExport implements FromQuery
{
    use Exportable;
    public $students;

    public function __construct(Collection $students)
    {
        // dd($students);
        $this->students = $students;
    }

    public function query()
    {
        return Student::whereKey($this->students->pluck('id')->toArray());
    }
}
