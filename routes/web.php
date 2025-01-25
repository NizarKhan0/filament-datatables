<?php

use App\Http\Controllers\DownloadPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{record}/pdf/download',[DownloadPdfController::class,'download'])->name('student.pdf.download');