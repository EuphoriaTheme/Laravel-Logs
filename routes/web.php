<?php
Route::get('/logs', [Pterodactyl\Http\Controllers\Admin\Extensions\{identifier}\{identifier}ExtensionController::class, 'showLogs'])->name('blueprint.extensions.laravellogs.wrapper.admin.logs');
Route::get('/logs/download', [Pterodactyl\Http\Controllers\Admin\Extensions\{identifier}\{identifier}ExtensionController::class, 'downloadLogs'])->name('blueprint.extensions.laravellogs.wrapper.admin.logs.download');
?>
