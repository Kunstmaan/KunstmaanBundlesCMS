<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Helper\Tabs\Tab;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\Helper\Tabs\PagePartTab;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

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
        $this->request = $request;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();

        if ($page instanceof HasPagePartsInterface) {
            $tabPane = $event->getTabPane();

            /* @var HasPagePartsInterface $page */
            foreach ($page->getPagePartAdminConfigurations() as $index => $pagePartAdminConfiguration) {
                $types = array();
                $data = array();
                $position = sizeof($tabPane->getTabs());
                if ($index == 0) {
                    /* @var Tab $propertiesTab */
                    $propertiesTab = $tabPane->getTabByTitle('Properties');

                    if (!is_null($propertiesTab)) {
                        $types = $propertiesTab->getTypes();
                        $data = $propertiesTab->getData();

                        $tabPane->removeTab($propertiesTab);
                        $position = 0;
                    }
                }
                $tabPane->addTab(new PagePartTab($pagePartAdminConfiguration->getName(), $page, $this->request, $this->em, $pagePartAdminConfiguration, $this->formFactory, $this->pagePartAdminFactory, $types, $data), $position);
            }
        }
    }

}
