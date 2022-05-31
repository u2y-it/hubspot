<?php 

namespace U2y\Hubspot\Services\Traits;

use HubSpot\Client\Crm\Contacts\Model\Filter as ModelFilter;
use HubSpot\Client\Crm\Contacts\Model\FilterGroup;

trait Filter
{
    private function getFilter(string $field, string $value)
    {
        $filter = new ModelFilter();
        $filter->setOperator('EQ')
            ->setPropertyName($field)
            ->setValue($value);
        return $filter;
    }

    private function getFilterContains(string $field, string $value)
    {
        $filter = new ModelFilter();
        $filter->setOperator('CONTAINS_TOKEN')
            ->setPropertyName($field)
            ->setValue($value);
        return $filter;
    }

    protected function filter(string $field, string $value)
    {
        $filter = $this->getFilter($field, $value);

        $filterGroup = new FilterGroup();
        $filterGroup->setFilters([$filter]);
        return $filterGroup;
    }

    protected function filterContains(string $field, string $value)
    {
        $filter = $this->getFilterContains($field, $value);

        $filterGroup = new FilterGroup();
        $filterGroup->setFilters([$filter]);
        return $filterGroup;
    }    
}
