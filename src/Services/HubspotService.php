<?php 

namespace U2y\Hubspot\Services;

use U2y\Hubspot\Models\HubspotToken;
use HubSpot\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HubspotService
{
    public $client;

    public function __construct($client = null)
    {
        if (!$client) {
            $client = $this->initClient();
        }
        $this->client = $client;
    }
    
    public function __call($name, $arguments = null)
    {
        $classname = 'U2y\\Hubspot\\Services\\Resources\\' . Str::ucfirst(Str::camel($name));
        if (class_exists($classname)) {
            return new $classname($this->client);
        }

        return $this->$name($arguments);
    }

    public function initClient()
    {
        $last_token = HubspotToken::orderBy('expire_at', 'desc')->first();
        if (!$last_token) {
            throw new \Exception('Not Hubspot token found. Please generate one');
        }

        if ($last_token->expire_at >= now()->subMinute()) {
            // refresh del token
            $response = self::refreshToken($last_token->refresh_token);
            $last_token = $this->saveTokenByResponse($response);
        }
        return Factory::createWithAccessToken($last_token->access_token);
    }   

    public static function requestAndSaveToken(string $code)
    {
        $response = self::requestToken($code);
        $token = self::saveTokenByResponse($response);
        return $token;
    }

    public static function requestToken(string $code)
    {
        try {            
            $response = Http::asForm()->post('https://api.hubapi.com/oauth/v1/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('hubspot.client_id'),
                'client_secret' => config('hubspot.client_secret'),
                'redirect_uri' => route('hubspot.auth_callback'),
                'code' => $code
            ]);
            
            $response->throwIf($response->failed());
        } catch (\Throwable $th) {
            throw $th;
        }

        return $response;
    }

    public static function saveTokenByResponse($response)
    {
        $resp = (object) $response->json();
        return HubspotToken::create([
            'access_token' => $resp->access_token,
            'refresh_token' => $resp->refresh_token,
            'expire_at' => now()->addSeconds($resp->expires_in)
        ]);
    }

    public static function refreshToken(string $refresh_token)
    {
        try {            
            $response = Http::asForm()->post('https://api.hubapi.com/oauth/v1/token', [
                'grant_type' => 'refresh_token',
                'client_id' => config('hubspot.client_id'),
                'client_secret' => config('hubspot.client_secret'),
                'redirect_uri' => route('hubspot.auth_callback'),
                'refresh_token' => $refresh_token
            ]);
            
            $response->throwIf($response->failed());
        } catch (\Throwable $th) {
            throw $th;
        }

        return $response;
    }
}
