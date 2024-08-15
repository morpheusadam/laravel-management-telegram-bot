<?php
use App\Http\Controllers\Docs;

Route::get('docs',[Docs::class,'installation'])->name('docs');
Route::get('docs/admin',[Docs::class,'administration'])->name('docs-admin');
Route::get('docs/tools',[Docs::class,'tools'])->name('docs-tools');
