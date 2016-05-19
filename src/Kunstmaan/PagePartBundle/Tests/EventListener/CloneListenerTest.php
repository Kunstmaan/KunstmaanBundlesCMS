<?php

namespace Kunstmaan\PagePartBundle\Tests\EventListener;

use Kunstmaan\PagePartBundle\EventListener\CloneListener;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;

/**
 * CloneListenerTest
 */
class CloneListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var PagePartAdminConfiguratorInterface
     */
    private $configurator;

    /**
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repo;

    /**
     * @var CloneListener
     */
    private $object;

    /**
     * @var PagePartConfigurationReaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var PageTemplateConfigurationService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $templateService;

    /**
     * Sets up the fixture.
     *
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::__construct
     */
    protected function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repo = $this->getMockBuilder(PagePartRefRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo('KunstmaanPagePartBundle:PagePartRef'))
            ->will($this->returnValue($this->repo));

        $this->configurator = new PagePartAdminConfigurator();
        $this->configurator->setContext('main');

        $this->reader = $this->getMock(PagePartConfigurationReaderInterface::class);
        $this->reader
            ->expects($this->any())
            ->method('getPagePartAdminConfigurators')
            ->will($this->returnValue([$this->configurator]));

        $this->reader
            ->expects($this->any())
            ->method('getPagePartContexts')
            ->will($this->returnValue([$this->configurator->getContext()]));

        $this->templateService = $this->getMockBuilder(PageTemplateConfigurationService::class)->disableOriginalConstructor()->getMock();

        $this->object = new CloneListener($this->em, $this->reader, $this->templateService);
    }

    /**
     * Tears down the fixture.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::postDeepCloneAndSave
     */
    public function testClonePagePart()
    {
        $entity = $this->getMock(HasPagePartsInterface::class);

        $clone = clone $entity;

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $event = new DeepCloneAndSaveEvent($entity, $clone);
        $this->object->postDeepCloneAndSave($event);
    }

    /**
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::postDeepCloneAndSave
     */
    public function testClonePageTemplate()
    {
        $entity = $this->getMock(HasPageTemplateInterface::class);

        /** @var HasPageTemplateInterface|\PHPUnit_Framework_MockObject_MockObject $clone */
        $clone = clone $entity;

        $entity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $clone->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $configuration = new PageTemplateConfiguration();
        $configuration->setId(1);
        $configuration->setPageId(1);

        $this->templateService->expects($this->once())
            ->method('findOrCreateFor')
            ->with($this->identicalTo($entity))
            ->will($this->returnValue($configuration));

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
