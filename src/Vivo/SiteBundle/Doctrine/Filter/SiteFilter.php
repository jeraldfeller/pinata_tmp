<?php

namespace Vivo\SiteBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SiteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the LocalAware interface
        if (!$targetEntity->reflClass->implementsInterface('Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface')) {
            return '';
        }

        $siteAssocMapping = $targetEntity->getAssociationMapping('site');

        if (!isset($siteAssocMapping['targetToSourceKeyColumns']['id'])) {
            throw new \Exception(sprintf("Could not find Target to source key column for '%s'", 'id'));
        }

        return $targetTableAlias.'.'.$siteAssocMapping['targetToSourceKeyColumns']['id'].' = '.$this->getParameter('site_id'); // getParameter applies quoting automatically
    }
}
