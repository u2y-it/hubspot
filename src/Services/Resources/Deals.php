<?php

namespace U2y\Hubspot\Services\Resources;

use GuzzleHttp\Client;
use Http;
use U2y\Hubspot\Services\Traits\Filter;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;
use U2y\Hubspot\Models\HubspotToken;
use U2y\Hubspot\Services\HubspotService;
use U2y\Hubspot\Services\Traits\FormatResponse;

class Deals
{
    use Filter, FormatResponse;

    public const STATUS_ERROR = 'error';

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

    // Questa dovrebbe essere la chiamata corretta con il client ufficiale di HS, ma pare non funzionare
    // public function listByStages(array $stages)
    // {
    //     $filterGroups = $this->filterByStages($stages);
    //     $searchRequest = new PublicObjectSearchRequest();
    //     $searchRequest->setFilterGroups([...$filterGroups]);
    //     $searchRequest->setLimit(100);
    //     $searchRequest->setSorts("ASCENDING");
    //     $searchRequest->setProperties(["hs_object_id"]);
    //     $searchRequest->setAfter(0);
    //     $deals = $this->client->crm()->deals()->searchApi()->doSearch($searchRequest);
    //     if (empty($deals->getResults())) {
    //         return null;
    //     }
    //     return $deals->getResults();
    // }

    // Usiamo questo metodo con un client ad hoc ed evitiamo di usare quello ufficiale di HS
    // perché sembra esserci un problema con il search sulle deals
    public function listByStages(array $stages)
    {
        // Forzo il refresh eventuale del token
        new HubspotService();
        // Prendo l'ultimo token dal db
        $last_token = HubspotToken::orderBy('expire_at', 'desc')->first();
        if (!$last_token) {
            throw new \Exception('Not Hubspot token found. Please generate one');
        }

        $result = Http::withToken($last_token->access_token)->post('https://api.hubapi.com/crm/v3/objects/deals/search', [ 
                'filterGroups' => $this->filterByStages($stages),
                'limit' => 100, 
                'after' => 0
            ]);

        $this->manageRequestErrors($result);
                
        $deals = $result->json()['results'];

        if (empty($deals)) {
            return null;
        }

        return $deals;
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

    private function manageRequestErrors($result)
    {
        if (isset($result->json()['status']) && $result->json()['status'] === self::STATUS_ERROR) {
            throw new \Exception($result->json()['message']);
        }
    }
}
