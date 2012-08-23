<?php

namespace Kunstmaan\FormBundle\Listener;

use Kunstmaan\AdminNodeBundle\Helper\Event\ConfigureActionMenuEvent;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class ConfigureActionsMenuListener
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Routing\Router $router
     */
    public function __construct(EntityManager $em, Router $router)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Helper\Event\ConfigureActionMenuEvent $event
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
