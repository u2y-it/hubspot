<?php 

namespace U2y\Hubspot\Traits;

trait FormatResponse
{
    public function formatResponse($response)
    {
        if(!$response) {
            return null;
        }
        return json_decode(json_encode($response));
    }
}