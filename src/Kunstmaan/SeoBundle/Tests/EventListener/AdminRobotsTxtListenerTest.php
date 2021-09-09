<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\Tests\EventListener;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Kunstmaan\SeoBundle\EventListener\AdminRobotsTxtListener;
use PHPUnit\Framework\TestCase;

class AdminRobotsTxtListenerTest extends TestCase
{
    private $repoMock;
    private const CONTENT = 'User-agent: *
Allow: /';

    public function testShouldSetContentWhenEntityExists()
    {
        $filled = new Robots();
        $filled->setRobotsTxt(self::CONTENT);

        $this->repoMock = $this->createMock(EntityRepository::class);
        $this->repoMock->expects($this->any())
            ->method('findOneBy')
            ->with([])
            ->willReturn($filled);

        $event = new RobotsEvent();
        $listener = new AdminRobotsTxtListener($this->repoMock);
        $listener->__invoke($event);

        $this->assertEquals(self::CONTENT, $event->getContent());
    }

    public function testShouldDoNothingWhenEntityMissing()
    {
        $this->repoMock = $this->createMock(EntityRepository::class);
        $this->repoMock->expects($this->any())
            ->method('findOneBy')
            ->with([])
            ->willReturn(null);

        $event = new RobotsEvent('untouched');
        $listener = new AdminRobotsTxtListener($this->repoMock);
        $listener->__invoke($event);

        $this->assertEquals('untouched', $event->getContent());
    }

    public function testShouldDoNothingWhenEntityEmpty()
    {
        $empty = new Robots();

        $this->repoMock = $this->createMock(EntityRepository::class);
        $this->repoMock->expects($this->any())
            ->method('findOneBy')
            ->with([])
            ->willReturn($empty);

        $event = new RobotsEvent('untouched');
        $listener = new AdminRobotsTxtListener($this->repoMock);
        $listener->__invoke($event);

        $this->assertEquals('untouched', $event->getContent());
    }
}
