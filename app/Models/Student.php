<?php

namespace App\Models;

//auth ni custom sendiri
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable
{
    use HasFactory;

    //ini untuk define auth guard student panel
    protected $guard = "student";


    protected $fillable = [
        'class_id',
        'section_id',
        'name',
        'email',
        'address',
        'phone_number',
        'password',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
