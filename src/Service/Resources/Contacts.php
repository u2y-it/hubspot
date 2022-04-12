<?php 

namespace U2y\Hubspot\Services\Hubspot\Resources;

use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Contacts\Model\FilterGroup;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;

class Contacts
{
    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function createOrUpdateByEmail(string $email, array $resource)
    {
        $contact = $this->getByEmail($email);
        
        $contact_props = new SimplePublicObjectInput();
        $contact_props->setProperties(
            $resource
        );

        if ($contact) {
            // aggiorno il contatto
            return $this->client->crm()->contacts()->basicApi()->update($contact->getId(), $contact_props);
        }
        // creo il contatto
        return $this->client->crm()->contacts()->basicApi()->create($contact_props);
    }

    public function getByEmail(string $email)
    {
        $filterGroup = $this->filterByEmail($email);
        $searchRequest = $this->createSearchRequest($filterGroup);
        $contactsPage = $this->client->crm()->contacts()->searchApi()->doSearch($searchRequest);

        if (empty($contactsPage->getResults())) {
            return null;
        }

        return $contactsPage->getResults()[0];
    }

    private function getFilter(string $field, string $value)
    {
        $filter = new Filter();
        $filter->setOperator('EQ')
            ->setPropertyName($field)
            ->setValue($value);
        return $filter;
    }

    private function filter(string $field, string $value)
    {
        $filter = $this->getFilter($field, $value);

        $filterGroup = new FilterGroup();
        $filterGroup->setFilters([$filter]);
        return $filterGroup;
    }

    private function filterByEmail(string $email)
    {
        return $this->filter('email', $email);
    }

    private function createSearchRequest($filterGroup)
    {
        $searchRequest = new PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([$filterGroup]);
        return $searchRequest;
    }
}
