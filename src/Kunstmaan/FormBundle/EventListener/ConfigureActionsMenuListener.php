<?php

namespace Kunstmaan\FormBundle\EventListener;

use Kunstmaan\AdminNodeBundle\Event\ConfigureActionMenuEvent;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\Router;

/**
 * An event listener to add a formsubmissions link to the submenu of nodes.
 */
class ConfigureActionsMenuListener
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param EntityManager $em     The entity manager
     * @param Router        $router The router
     */
    public function __construct(EntityManager $em, Router $router)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * Configure the form submissions link on top of the form in the sub action menu
     *
     * @param ConfigureActionMenuEvent $event
     */
    public function onSubActionMenuConfigure(ConfigureActionMenuEvent $event)
    {
        $menu = $event->getMenu();
        $activeNodeVersion = $event->getActiveNodeVersion();

        if (!is_null($activeNodeVersion)) {
            $page = $activeNodeVersion->getRef($this->em);
            if ($page instanceof AbstractFormPage) {
                $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();
                $menu->addChild('subaction.formsubmissions', array('uri' => $this->router->generate('KunstmaanFormBundle_formsubmissions_list', array('nodetranslationid' => $activeNodeTranslation->getId()))));
            }
        }
    }

}
