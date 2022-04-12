<?php 

app('router')->prefix('hubspot')->middleware('auth:admin')->group(function() {
    app('router')->get('', [HubspotController::class, 'index'])->name('hubspot.auth');
    app('router')->get('auth-callback', [HubspotController::class, 'callback'])->name('hubspot.auth_callback')->withoutMiddleware('auth:admin');
});