<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * PagePartAdminFactory constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(/* RequestStack */ $requestStack)
    {
        if ($requestStack instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanPagePartBundle 5.1 and will be removed in KunstmaanPagePartBundle 6.0.',
                E_USER_DEPRECATED
            );

            $this->container = $requestStack;
            $this->requestStack = $requestStack->get(RequestStack::class);

            return;
        }

        $this->requestStack = $requestStack;
    }

    /**
     * @param PagePartAdminConfiguratorInterface $configurator The configurator
     * @param EntityManagerInterface             $em           The entity manager
     * @param HasPagePartsInterface              $page         The page
     * @param string|null                        $context      The context
     *
     * @return PagePartAdmin
     */
    public function createList(
        PagePartAdminConfiguratorInterface $configurator,
        EntityManagerInterface $em,
        HasPagePartsInterface $page,
        $context = null
    ) {
        return new PagePartAdmin($configurator, $em, $page, $context, $this->requestStack);
    }
}
