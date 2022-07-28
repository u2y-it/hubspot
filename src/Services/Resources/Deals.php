<?php 

namespace U2y\Hubspot\Services\Resources;

use U2y\Hubspot\Services\Traits\Filter;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;

class Deals
{
    use Filter;
    
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

    public function listByStages(array $stages)
    {
        $filterGroups = $this->filterByStages($stages);
        $searchRequest = $this->createSearchRequest($filterGroups);
        $dealsPage = $this->client->crm()->deals()->searchApi()->doSearch($searchRequest);

        if (empty($dealsPage->getResults())) {
            return null;
        }

        return $dealsPage->getResults();
    }

    public function formattedListByStages(array $stages)
    {
        $list = $this->listByStages($stages);
        if(!$list) {
            return null;
        }
        return json_decode(json_encode($list));
    }

    private function filterByStages(array $stages): array
    {
        return [
            $this->filterIn('dealstage', $stages),
        ];
    }

    private function createSearchRequest(array $filterGroups)
    {
        $searchRequest = new PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([...$filterGroups]);
        return $searchRequest;
    }
}
