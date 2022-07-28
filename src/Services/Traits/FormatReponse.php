<?php 

namespace U2y\Hubspot\Traits;

trait FormatReponse
{
    public function format($response)
    {
        if(!$response) {
            return null;
        }
        return json_decode(json_encode($response));
    }
}