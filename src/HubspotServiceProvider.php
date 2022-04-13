<?php 

namespace U2y\Hubspot;

class HubspotServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $configPath = __DIR__ . '/../config/hubspot.php';
        $this->mergeConfigFrom($configPath, 'hubspot');
    }
    
    public function boot()
    {
        $this->loadRoutesFrom(realpath(__DIR__ . '/hubspot-routes.php'));        
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hubspot');
        
        $this->publishes([
            __DIR__.'/../config/hubspot.php' => config_path('hubspot.php'),
        ]);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/hubspot'),
        ]);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/hubspot'),
        ], 'public');
    }
}
