<?php

namespace Kunstmaan\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Kunstmaan\SeoBundle\Entity\Seo,
    Kunstmaan\SeoBundle\Form\SeoType,
    Kunstmaan\SeoBundle\Form\SocialType;

use Kunstmaan\NodeBundle\Helper\Tabs\Tab,
    Kunstmaan\NodeBundle\Event\AdaptFormEvent;

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

        $seoTab = new Tab('SEO');
        $seoTab->addType('seo', new SeoType(), $seo);
        $event->getTabPane()->addTab($seoTab);

        $socialTab = new Tab('Social');
        $socialTab->addType('social', new SocialType(), $seo);
        $event->getTabPane()->addTab($socialTab);
    }

}
