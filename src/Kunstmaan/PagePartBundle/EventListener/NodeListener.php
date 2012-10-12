<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\Tabs\PagePartTab;

class NodeListener
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
     * @var Request
     */
    private $request;

    /**
     * @param Request              $request              The request
     * @param EntityManager        $em                   The entity manager
     * @param FormFactoryInterface $formFactory          The form factory
     * @param PagePartAdminFactory $pagePartAdminFactory The page part admin factory
     */
    public function __construct(Request $request, EntityManager $em, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();

        if (method_exists($page, 'getPagePartAdminConfigurations')) {
            $tabPane = $event->getTabPane();

            /*
             * @var AbstractPagePartAdminConfigurator $pagePartAdminConfiguration
             */
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                // @todo first tab should be merges with PropertiesTab
                $tabPane->addTab(new PagePartTab($pagePartAdminConfiguration->getName(), $page, $this->request, $this->em, $pagePartAdminConfiguration, $this->formFactory, $this->pagePartAdminFactory));
            }
        }
    }

}
