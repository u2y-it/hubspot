<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubspot oauth</title>
    
    <link rel="stylesheet" href="{{ asset('vendor/hubspot/css/style.css') }}">
</head>
<body>
    <div class="w-full">

        @if(session()->has('message'))
        <div class="bg-green-normal break-all mb-12 mt-12 mx-auto p-8 rounded-2xl w-3/4">
            <div>
                <p class="text-sm text-white">
                    {{ session()->get('message') }}
                </p>
            </div>
        </div>
        @endif

        @if($token)
        <div class="mt-12 text-center ">
            <p class="text-md">
                Token Hubspot
            </p>
            <div class="mx-auto flex flex-col gap-4 w-8/12 items-center justify-center">
                <div class="gap-12">
                    <div>
                        Access token
                    </div>
                    <div>
                        {{ $token->access_token }}
                    </div>
                </div>
                <div class="gap-12">
                    <div>
                        Refresh token
                    </div>
                    <div>
                        {{ $token->access_token }}
                    </div>
                </div>
                <div class="gap-12">
                    <div>
                        Expiration
                    </div>
                    <div>
                        {{ $token->expire_at->timezone('Europe/Rome')->format('d/m/Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="w-full flex justify-center items-center gap-12 flex-col py-12">
            <p class="text-md">
                Autorizza Hubspot
            </p>
            @if (config('hubspot.client_id'))
                <div>
                    <a class="bg-green-normal px-10 py-4 rounded-2xl text-white" href="{{ $hubspot_url }}">Install</a>
                </div>
            @else
                <div>
                    <p>
                        Hubspot Client ID not found
                    </p>
                </div>
            @endif
        </div>
        @endif
    </div>
</body>
</html>