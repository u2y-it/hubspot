<?php

namespace U2y\Hubspot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hubspot\HubspotToken;
use App\Services\Hubspot\HubspotService;
use HubSpot\Factory;
use HubSpot\Client\Auth\OAuth\ApiException;
use HubSpot\Utils\OAuth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HubspotController extends Controller
{
    public function index()
    {
        $token = HubspotToken::orderBy('expire_at', 'desc')->first();
        
        return view('hubspot::oauth')
            ->with('hubspot_url', OAuth2::getAuthUrl(
                config('hubspot.client_id'), 
                route('hubspot.auth_callback'), [
                    'crm.objects.contacts.read',
                    'crm.objects.contacts.write'
                ]
            ))
            ->with('token', $token);
    }

    public function callback(Request $request)
    {
        try {
            $token = HubspotService::requestAndSaveToken($request->code);
        } catch (\Exception $e) {
            echo "Exception when calling access_tokens_api->get_access_token: ", $e->getMessage();
        }

        echo 'Token generato: ' . $token->access_token;
    }
}
