<?php

namespace Kunstmaan\PagePartBundle\Tests\EventListener;

use Kunstmaan\PagePartBundle\EventListener\CloneListener;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;

/**
 * CloneListenerTest
 */
class CloneListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Symfony\Component\HttpKernel\KernelInterface
     */
    protected $kernel;

    /**
     * @var Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator
     */
    protected $configurator;

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repo;

    /**
     * @var CloneListener
     */
    protected $object;

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

        $this->repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(array('findOrCreateFor', 'copyPageParts'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(
                $this->logicalOr(
                    'KunstmaanPagePartBundle:PagePartRef',
                    'KunstmaanPagePartBundle:PageTemplateConfiguration'
                )
            )
            ->will($this->returnValue($this->repo));

        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');

        $this->configurator = $this->getMock('Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator');
        $this->configurator->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue('main'));

        $this->object = new CloneListener($this->em, $this->kernel);
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
        $entity = $this->getMockBuilder('Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface')
            ->setMethods(array('getId', 'getPagePartAdminConfigurations'))
            ->getMock();

        $entity->expects($this->any())
            ->method('getPagePartAdminConfigurations')
            ->will($this->returnValue(array($this->configurator)));

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
        $entity = $this->getMockBuilder('Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface')
            ->setMethods(array('getId', 'getPageTemplates', 'getPagePartAdminConfigurations'))
            ->getMock();

        $entity->expects($this->any())
            ->method('getPagePartAdminConfigurations')
            ->will($this->returnValue(array($this->configurator)));

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

        $this->repo->expects($this->once())
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
