<?php

namespace Kunstmaan\FormBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * An event listener to add a formsubmissions link to the submenu of nodes.
 */
class ConfigureActionsMenuListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param EntityManagerInterface $em     The entity manager
     * @param RouterInterface        $router The router
     */
    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * Configure the form submissions link on top of the form in the sub action menu
     */
    public function onSubActionMenuConfigure(ConfigureActionMenuEvent $event)
    {
        $menu = $event->getMenu();
        $activeNodeVersion = $event->getActiveNodeVersion();

        if (!is_null($activeNodeVersion)) {
            $page = $activeNodeVersion->getRef($this->em);
            if ($page instanceof AbstractFormPage) {
                $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();
                $menu->addChild('subaction.formsubmissions', ['uri' => $this->router->generate('KunstmaanFormBundle_formsubmissions_list', ['nodeTranslationId' => $activeNodeTranslation->getId()])]);
            }
        }
    }
}
