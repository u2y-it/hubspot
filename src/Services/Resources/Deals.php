<?php

namespace U2y\Hubspot\Services\Resources;

use GuzzleHttp\Client;
use Http;
use HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest;
use U2y\Hubspot\Models\HubspotToken;
use U2y\Hubspot\Services\HubspotService;
use U2y\Hubspot\Services\Traits\FormatResponse;
use HubSpot\Client\Crm\Deals\Model\Filter as ModelFilter;
use HubSpot\Client\Crm\Deals\Model\FilterGroup;
use U2y\Hubspot\Services\Traits\DealsFilter;

class Deals
{
    use DealsFilter, FormatResponse;

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

    // Usiamo questo metodo con un client ad hoc ed evitiamo di usare quello ufficiale di HS
    // perché sembra esserci un problema con il search sulle deals
    public function listByStages(array $stages, array $options = [])
    {
        // Forzo il refresh eventuale del token
        new HubspotService();
        // Prendo l'ultimo token dal db
        $last_token = HubspotToken::orderBy('expire_at', 'desc')->first();
        if (!$last_token) {
            throw new \Exception('Not Hubspot token found. Please generate one');
        }

        $result = Http::withToken($last_token->access_token)->post('https://api.hubapi.com/crm/v3/objects/deals/search', 
            array_merge(
                [ 
                    'filterGroups' => $this->filterByStages($stages),
                    'limit' => 100, 
                    'after' => 0
                ],
                $options
            )
        );

        $this->manageRequestErrors($result);
                
        $deals = $result->json()['results'];

        if (empty($deals)) {
            return null;
        }

        return $deals;
    }

    public function create()
    {
        // TODO
        return null;
    }

    public function formattedListByStages(array $stages, array $options = [])
    {
        return $this->formatResponse($this->listByStages($stages, $options));
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
