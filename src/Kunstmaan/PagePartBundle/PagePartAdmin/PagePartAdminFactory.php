<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
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
     * @param PagePartAdminConfiguratorInterface $configurator The configurator
     * @param EntityManagerInterface             $em           The entity manager
     * @param HasPagePartsInterface              $page         The page
     * @param string|null                        $context      The context
     *
     * @return PagePartAdmin
     */
    public function createList(PagePartAdminConfiguratorInterface $configurator, EntityManagerInterface $em, HasPagePartsInterface $page, $context = null)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $this->container);
    }
}
