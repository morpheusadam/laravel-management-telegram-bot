<?php
use App\Http\Controllers\Agency;

Route::get('setting/landing',[Agency::class,'get_agency_landing_page_data'])->middleware(['auth'])->name('agency-landing-editor');
Route::post('setting/landing',[Agency::class,'submit_agency_landing_form_data'])->middleware(['auth','XssSanitizer'])->name('agency-landing-editor-action');
Route::get('setting/landing/reset',[Agency::class,'reset_editor'])->middleware(['auth'])->name('agency-landing-editor-reset');
Route::post('setting/landing/media/upload',[Agency::class,'upload_media'])->middleware(['auth'])->name('agency-landing-upload-media');


// Route::languages(Multilanguage::class);
Route::group(config('translation.route_group_config') + ['namespace' => 'App\\Http\\Controllers'], function ($router) {
    $router->get(config('translation.ui_url'), 'Multilanguage@index')
        ->middleware(['auth'])->name('languages.index');

    $router->get(config('translation.ui_url').'/create', 'Multilanguage@create')
        ->middleware(['auth'])->name('languages.create');

    $router->get(config('translation.ui_url').'/edit/{language}', 'Multilanguage@edit')
        ->middleware(['auth'])->name('languages.edit');

    $router->get(config('translation.ui_url').'/delete/{locale?}', 'Multilanguage@delete')
        ->middleware(['auth'])->name('languages.delete');

    $router->post(config('translation.ui_url'), 'Multilanguage@store')
        ->middleware(['auth'])->name('languages.store');

    $router->get(config('translation.ui_url').'/download/{language}', 'Multilanguage@download_languages')
        ->middleware(['auth'])->name('languages.download');

    $router->get(config('translation.ui_url').'/{language}/translations', 'Multilanguage@translation_index')
        ->middleware(['auth'])->name('languages.translations.index');

    $router->post(config('translation.ui_url').'/{language}', 'Multilanguage@update_translation')
        ->middleware(['auth'])->name('languages.translations.update');

    $router->get(config('translation.ui_url').'/{language}/translations/create', 'Multilanguage@create_translation')
        ->middleware(['auth'])->name('languages.translations.create');


    $router->post(config('translation.ui_url').'/{language}/translations', 'Multilanguage@store_translation')
        ->middleware(['auth'])->name('languages.translations.store');

    $router->get(config('translation.ui_url').'/new-group/{locale?}/{group?}', 'Multilanguage@create_new_group')
        ->middleware(['auth'])->name('languages.translations.create-new-group');

    $router->get(config('translation.ui_url').'/run-command/{language}', 'Multilanguage@run_artisan')
        ->middleware(['auth'])->name('languages.translations.find-language');

    $router->get(config('translation.ui_url').'/compile/{language}', 'Multilanguage@compile_language')
        ->middleware(['auth'])->name('languages.translations.compile');
});
