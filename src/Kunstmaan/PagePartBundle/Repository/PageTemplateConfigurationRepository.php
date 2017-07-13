<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * PageTemplateConfigurationRepository
 */
class PageTemplateConfigurationRepository extends EntityRepository
{

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findFor(HasPageTemplateInterface $page)
    {
        return $this->findOneBy(array('pageId' => $page->getId(), 'pageEntityName' => ClassLookup::getClass($page)));
    }

}
