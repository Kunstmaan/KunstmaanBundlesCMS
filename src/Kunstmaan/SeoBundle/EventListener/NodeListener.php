<?php

namespace Kunstmaan\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Kunstmaan\SeoBundle\Entity\Seo;
use Kunstmaan\NodeBundle\Helper\Tabs\Tab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;

/**
 * This will add a seo tab on each page
 */
class NodeListener
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        /* @var Seo $seo */
        $seo = $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($event->getPage());

        $seoTab = new Tab('Seo');
        $seoTab->addType('seo', $seo->getDefaultAdminType(), $seo);
        $event->getTabPane()->addTab($seoTab);
    }

}
