<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormFactoryInterface;

use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\Tabs\PagePartTab;

class EditNodeListener
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var PagePartAdminFactory
     */
    private $pagePartAdminFactory;

    /**
     * @param EntityManager $em
     * @param FormFactoryInterface $formFactory
     * @param PagePartAdminFactory $pagePartAdminFactory
     */
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function buildForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();

        if (method_exists($page, 'getPagePartAdminConfigurations')) {
            $tabPane = $event->getTabPane();

            /*
             * @var AbstractPagePartAdminConfigurator $pagePartAdminConfiguration
             */
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                $tabPane->addTab(new PagePartTab($pagePartAdminConfiguration->getName(), $page, $this->em, $pagePartAdminConfiguration, $this->formFactory, $this->pagePartAdminFactory));
            }
        }
    }

}
