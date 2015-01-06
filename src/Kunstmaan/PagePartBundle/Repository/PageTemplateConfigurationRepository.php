<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReader;

/**
 * PageTemplateConfigurationRepository
 */
class PageTemplateConfigurationRepository extends EntityRepository implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
     * @param HasPageTemplateInterface $page The page
     *
     * @return PageTemplateConfiguration
     */
    public function findOrCreateFor(HasPageTemplateInterface $page)
    {
        $pageTemplateConfiguration = $this->findFor($page);

        if (is_null($pageTemplateConfiguration)) {
            $pageTemplateConfigurationReader = new PageTemplateConfigurationReader($this->container->get('kernel'));
            $pageTemplates = $this->pageTemplates = $pageTemplateConfigurationReader->getPageTemplates($page);
            $names = array_keys($pageTemplates);
            $defaultPageTemplate = $pageTemplates[$names[0]];

            $pageTemplateConfiguration = new PageTemplateConfiguration();
            $pageTemplateConfiguration->setPageId($page->getId());
            $pageTemplateConfiguration->setPageEntityName(ClassLookup::getClass($page));
            $pageTemplateConfiguration->setPageTemplate($defaultPageTemplate->getName());
        }

        return $pageTemplateConfiguration;
    }
}
