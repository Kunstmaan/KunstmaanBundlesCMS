<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * PagePartAdminFactory
 */
class PagePartAdminFactory
{
    /**
     * @param AbstractPagePartAdminConfigurator $configurator The configurator
     * @param EntityManager                     $em           The entity manager
     * @param HasPagePartsInterface             $page         The page
     * @param string|null                       $context      The context
     * @param Container                         $container    The container
     *
     * @return PagePartAdmin
     */
    public function createList(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, HasPagePartsInterface $page, $context = null, Container $container = null)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $container);
    }
}
