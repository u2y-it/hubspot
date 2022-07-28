<?php 

namespace U2y\Hubspot\Services\Resources;

class Deals
{
    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function list()
    {
        $dealsPage = $this->client->crm()->deals()->basicApi()->getPage(1000, null, null, null, null, false);;

        if (empty($dealsPage->getResults())) {
            return null;
        }

        return $dealsPage->getResults()[0];
    }
}
