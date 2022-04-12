<?php

use U2y\Hubspot\Http\Controllers\HubspotController;

app('router')->prefix('hubspot')->middleware(['web', config('hubspot.auth_middleware')])->group(function() {
    app('router')->get('', [HubspotController::class, 'index'])->name('hubspot.auth');
    app('router')->get('auth-callback', [HubspotController::class, 'callback'])->name('hubspot.auth_callback')->withoutMiddleware(config('hubspot.auth_middleware'));
});