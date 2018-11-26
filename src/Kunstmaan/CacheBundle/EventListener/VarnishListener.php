<?php

namespace Kunstmaan\CacheBundle\EventListener;

use Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VarnishListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * VarnishListener constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RouterInterface               $router
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
    }

    /**
     * @param ConfigureActionMenuEvent $event
     */
    public function onActionMenuConfigure(ConfigureActionMenuEvent $event)
    {
        $menu = $event->getMenu();
        $activeNodeVersion = $event->getActiveNodeVersion();

        if ($activeNodeVersion !== null && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $activeNode = $activeNodeVersion->getNodeTranslation()->getNode();

            $menu->addChild(
                'kunstmaan_cache.varnish.ban.menu',
                [
                    'uri' => $this->router->generate(
                        'kunstmaancachebundle_varnish_ban',
                        [
                            'node' => $activeNode->getId(),
                        ]
                    ),
                    'linkAttributes' => [
                        'class' => 'btn btn-default btn--raise-on-hover',
                    ],
                ]
            );
        }
    }
}
