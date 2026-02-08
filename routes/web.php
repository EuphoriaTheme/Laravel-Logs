<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Admin\Extensions\{identifier}\{identifier}ExtensionController;

/*
 * Blueprint registers this router from `conf.yml` (requests.routers.web).
 * If you change `info.identifier`, update these route names and any references in views.
 */
Route::get('/logs', [{identifier}ExtensionController::class, 'index'])
    ->name('blueprint.extensions.laravellogs.wrapper.admin.logs');

Route::get('/logs/download', [{identifier}ExtensionController::class, 'downloadLogs'])
    ->name('blueprint.extensions.laravellogs.wrapper.admin.logs.download');
