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
        // max 100 per page
        $dealsPage = $this->client->crm()->deals()->basicApi()->getPage(100, null, null, null, null, false);

        if (empty($dealsPage->getResults())) {
            return null;
        }

        return $dealsPage->getResults();
    }

    public function formattedList()
    {
        $list = $this->list();
        if(!$list) {
            return null;
        }
        return json_decode(json_encode($list));
    }
}
