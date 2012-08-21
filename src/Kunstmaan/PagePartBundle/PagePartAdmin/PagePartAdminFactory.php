<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class PagePartAdminFactory
{
    /**
     * @param AbstractPagePartAdminConfigurator $configurator
     * @param EntityManager                     $em
     * @param AbstractPage                      $page
     * @param string|null                       $context
     * @param Container                         $container
     *
     * @return PagePartAdmin
     */
    public function createList(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, AbstractPage $page, $context = null, Container $container)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $container);
    }
}
