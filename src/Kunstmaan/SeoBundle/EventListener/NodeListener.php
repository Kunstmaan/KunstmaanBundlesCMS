<?php

namespace Kunstmaan\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\SeoBundle\Entity\Seo;
use Kunstmaan\SeoBundle\Form\SeoType;
use Kunstmaan\SeoBundle\Form\SocialType;

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
        if ($event->getPage() instanceof HasNodeInterface && !$event->getPage()->isStructureNode()) {
            /* @var Seo $seo */
            $seo = $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($event->getPage());

            $seoWidget = new FormWidget();
            $seoWidget->addType('seo', SeoType::class, $seo);
            $event->getTabPane()->addTab(new Tab('seo.tab.seo.title', $seoWidget));

            $socialWidget = new FormWidget();
            $socialWidget->addType('social', SocialType::class, $seo);
            $socialWidget->setTemplate('KunstmaanSeoBundle:Admin\Social:social.html.twig');
            $event->getTabPane()->addTab(new Tab('seo.tab.social.title', $socialWidget));
        }
    }
}
