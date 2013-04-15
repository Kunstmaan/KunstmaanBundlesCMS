<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

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
     * @param EntityManager $em
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
            $pagePartAdminConfigurations = array();
            foreach ($originalEntity->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                if (is_string($pagePartAdminConfiguration)) {
                    $pagePartAdminConfigurations[] = $pagePartConfigurationReader->parse($pagePartAdminConfiguration);
                } else if (is_object($pagePartAdminConfiguration) && $pagePartAdminConfiguration instanceof AbstractPagePartAdminConfigurator) {
                    $pagePartAdminConfigurations[] = $pagePartAdminConfiguration;
                } else {
                    throw new \Exception("don't know how to handle the pagePartAdminConfiguration " . get_class($pagePartAdminConfiguration));
                }
            }
            foreach ($pagePartAdminConfigurations as $ppConfiguration) {
                $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts($this->em, $originalEntity, $clonedEntity, $ppConfiguration->getDefaultContext());
            }
        }
        if ($originalEntity instanceof HasPageTemplateInterface) {
            $clonedEntity = $event->getClonedEntity();
            $newPageTemplateConfiguration = clone $this->em->getRepository('KunstmaanPagePartBundle:PageTemplateConfiguration')->findOrCreateFor($originalEntity);
            $newPageTemplateConfiguration->setId(null);
            $newPageTemplateConfiguration->setPageId($clonedEntity->getId());
            $this->em->persist($newPageTemplateConfiguration);
        }
    }
}
