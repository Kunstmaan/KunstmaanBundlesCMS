<?php

namespace Kunstmaan\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\SeoBundle\Entity\Seo;

/**
 * This event will make sure the seo metadata is copied when a page is cloned
 */
class CloneListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CloneHelper
     */
    private $cloneHelper;

    /**
     * @param EntityManager $em          The entity manager
     * @param CloneHelper   $cloneHelper The clone helper
     */
    public function __construct(EntityManager $em, CloneHelper $cloneHelper)
    {
        $this->em = $em;
        $this->cloneHelper = $cloneHelper;
    }

    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof AbstractEntity) {
            /* @var Seo $seo */
            $seo = $this->em->getRepository('KunstmaanSeoBundle:Seo')->findFor($originalEntity);

            if (!is_null($seo)) {
                /* @var Seo $clonedSeo */
                $clonedSeo = $this->cloneHelper->deepCloneAndSave($seo);
                $clonedSeo->setRef($event->getClonedEntity());

                $this->em->persist($clonedSeo);
                $this->em->flush($clonedSeo);
            }
        }
    }
}
