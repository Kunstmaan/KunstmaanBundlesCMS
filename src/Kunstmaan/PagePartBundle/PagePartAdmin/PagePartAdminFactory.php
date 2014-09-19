<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PagePartAdminFactory
 */
class PagePartAdminFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param AbstractPagePartAdminConfigurator $configurator The configurator
     * @param EntityManager                     $em           The entity manager
     * @param HasPagePartsInterface             $page         The page
     * @param string|null                       $context      The context
     *
     * @return PagePartAdmin
     */
    public function createList(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, HasPagePartsInterface $page, $context = null)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $this->container);
    }
}
