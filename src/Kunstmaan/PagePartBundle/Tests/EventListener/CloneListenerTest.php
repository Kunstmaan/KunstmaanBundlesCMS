<?php

namespace Kunstmaan\PagePartBundle\Tests\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\EventListener\CloneListener;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CloneListenerTest extends TestCase
{
    /**
     * @var EntityManager|MockObject
     */
    private $em;

    /**
     * @var PagePartAdminConfiguratorInterface
     */
    private PagePartAdminConfigurator $configurator;

    /**
     * @var EntityRepository|MockObject
     */
    private $repo;

    private CloneListener $object;

    /**
     * @var PagePartConfigurationReaderInterface|MockObject
     */
    private $reader;

    /**
     * @var PageTemplateConfigurationService|MockObject
     */
    private $templateService;

    protected function setUp(): void
    {
        $this->em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repo = $this->getMockBuilder(PagePartRefRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em
            ->method('getRepository')
            ->with($this->equalTo(PagePartRef::class))
            ->willReturn($this->repo);

        $this->configurator = new PagePartAdminConfigurator();
        $this->configurator->setContext('main');

        $this->reader = $this->createMock(PagePartConfigurationReaderInterface::class);
        $this->reader
            ->method('getPagePartAdminConfigurators')
            ->willReturn([$this->configurator]);

        $this->reader
            ->method('getPagePartContexts')
            ->willReturn([$this->configurator->getContext()]);

        $this->templateService = $this->getMockBuilder(PageTemplateConfigurationService::class)->disableOriginalConstructor()->getMock();

        $this->object = new CloneListener($this->em, $this->reader, $this->templateService);
    }

    public function testClonePagePart()
    {
        $entity = $this->createMock(HasPagePartsInterface::class);

        $clone = clone $entity;

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $event = new DeepCloneAndSaveEvent($entity, $clone);
        $this->object->postDeepCloneAndSave($event);
    }

    public function testClonePageTemplate()
    {
        $entity = $this->createMock(HasPageTemplateInterface::class);

        /** @var HasPageTemplateInterface|MockObject $clone */
        $clone = clone $entity;

        $entity
            ->method('getId')
            ->willReturn(1);

        $clone
            ->method('getId')
            ->willReturn(2);

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $configuration = new PageTemplateConfiguration();
        $configuration->setId(1);
        $configuration->setPageId(1);

        $this->templateService->expects($this->once())
            ->method('findOrCreateFor')
            ->with($this->identicalTo($entity))
            ->willReturn($configuration);

        $newConfiguration = clone $configuration;
        $newConfiguration->setId(null);
        $newConfiguration->setPageId($clone->getId());

        $this->em->expects($this->once())
            ->method('persist')
            ->with($newConfiguration);

        $event = new DeepCloneAndSaveEvent($entity, $clone);
        $this->object->postDeepCloneAndSave($event);
    }
}
