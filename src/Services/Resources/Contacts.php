<?php 

namespace U2y\Hubspot\Services\Resources;

use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;
use U2y\Hubspot\Services\Traits\Filter;

class Contacts
{
    use Filter;

    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function create(array $resource)
    {        
        $contact_props = new SimplePublicObjectInput();
        $contact_props->setProperties(
            $resource
        );
        // creo il contatto
        return $this->client->crm()->contacts()->basicApi()->create($contact_props);
    }

    public function updateByEmail(string $email, array $resource)
    {     
        $contact = $this->getByEmail($email);   
        $contact_props = new SimplePublicObjectInput();
        $contact_props->setProperties(
            $resource
        );
        // aggiorno il contatto
        return $this->client->crm()->contacts()->basicApi()->update($contact->getId(), $contact_props);
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
        $filterGroups = $this->filterByEmail($email);
        $searchRequest = $this->createSearchRequest($filterGroups);
        $contactsPage = $this->client->crm()->contacts()->searchApi()->doSearch($searchRequest);

        if (empty($contactsPage->getResults())) {
            return null;
        }

        return $contactsPage->getResults()[0];
    }

    /**
     * Filtro per email principale e anche per le secondarie
     *
     * @param string $email
     * @return array
     */
    private function filterByEmail(string $email): array
    {
        return [
            $this->filter('email', $email),
            $this->filterContains('hs_additional_emails', $email)
        ];
    }

    private function createSearchRequest(array $filterGroups)
    {
        $searchRequest = new PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([...$filterGroups]);
        return $searchRequest;
    }
}
