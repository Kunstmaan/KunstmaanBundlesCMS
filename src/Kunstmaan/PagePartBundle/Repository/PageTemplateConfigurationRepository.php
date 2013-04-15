<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;

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

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findOrCreateFor(HasPageTemplateInterface $page, PageTemplate $defaultPageTemplate)
    {
        $pageTemplateConfiguration = $this->findFor($page);

        if (is_null($pageTemplateConfiguration)) {
            $pageTemplateConfiguration = new PageTemplateConfiguration();
            $pageTemplateConfiguration->setPageId($page->getId());
            $pageTemplateConfiguration->setPageEntityName(ClassLookup::getClass($page));
            $pageTemplateConfiguration->setPageTemplate($defaultPageTemplate->getName());
        }

        return $pageTemplateConfiguration;
    }
}
