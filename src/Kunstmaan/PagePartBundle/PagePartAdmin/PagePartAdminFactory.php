<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
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
     * @param AbstractPage                      $page         The page
     * @param string|null                       $context      The context
     * @param Container                         $container    The container
     *
     * @return PagePartAdmin
     */
    public function createList(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, AbstractPage $page, $context = null, Container $container = null)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $container);
    }
}
