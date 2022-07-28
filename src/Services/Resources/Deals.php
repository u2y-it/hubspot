<?php 

namespace U2y\Hubspot\Services\Resources;

use U2y\Hubspot\Services\Traits\Filter;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;
use U2y\Hubspot\Services\Traits\FormatResponse;

class Deals
{
    use Filter, FormatResponse;
    
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
        return $this->formatResponse($this->list());
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
        return $this->formatResponse($this->listByStages($stages));
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
