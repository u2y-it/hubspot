<?php

namespace U2y\Hubspot\Http\Controllers;

use App\Http\Controllers\Controller;
use HubSpot\Utils\OAuth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use U2y\Hubspot\Models\HubspotToken;
use U2y\Hubspot\Services\HubspotService;

class HubspotController extends Controller
{
    public function index()
    {
        $token = HubspotToken::orderBy('expire_at', 'desc')->first();

        return view('hubspot::oauth')
            ->with('hubspot_url', OAuth2::getAuthUrl(
                config('hubspot.client_id'),
                route('hubspot.auth_callback'),
                config('hubspot.scopes')
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

        Session::flash('message', 'Token generato: ' . $token->access_token);

        return redirect()->route('hubspot.auth');
    }
}
