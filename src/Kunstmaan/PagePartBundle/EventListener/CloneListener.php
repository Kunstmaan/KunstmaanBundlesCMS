<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;

/**
 * This event will make sure pageparts are being copied when deepClone is done on an entity implementing hasPagePartsInterface
 */
class CloneListener
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
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof HasPagePartsInterface) {
            $clonedEntity = $event->getClonedEntity();

            $ppConfigurations = $originalEntity->getPagePartAdminConfigurations();
            foreach ($ppConfigurations as $ppConfiguration) {
                $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts($this->em, $originalEntity, $clonedEntity, $ppConfiguration->getDefaultContext());
            }
        }
    }

}
