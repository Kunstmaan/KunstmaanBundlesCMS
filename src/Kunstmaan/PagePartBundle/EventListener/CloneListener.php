<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;

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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param EntityManager   $em     The entity manager
     * @param KernelInterface $kernel The kernel
     */
    public function __construct(EntityManager $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;
    }

    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof HasPagePartsInterface) {
            $clonedEntity = $event->getClonedEntity();

            $pagePartConfigurationReader = new PagePartConfigurationReader($this->kernel);
            $contexts = $pagePartConfigurationReader->getPagePartContexts($originalEntity);
            foreach ($contexts as $context) {
                $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts($this->em, $originalEntity, $clonedEntity, $context);
            }
        }
        if ($originalEntity instanceof HasPageTemplateInterface) {
            $clonedEntity = $event->getClonedEntity();
            $PageTemplateConfigurationRepo = $this->em->getRepository('KunstmaanPagePartBundle:PageTemplateConfiguration');
            $PageTemplateConfigurationRepo->setContainer($this->kernel->getContainer());
            $newPageTemplateConfiguration = clone $PageTemplateConfigurationRepo->findOrCreateFor($originalEntity);
            $newPageTemplateConfiguration->setId(null);
            $newPageTemplateConfiguration->setPageId($clonedEntity->getId());
            $this->em->persist($newPageTemplateConfiguration);
        }
    }
}
