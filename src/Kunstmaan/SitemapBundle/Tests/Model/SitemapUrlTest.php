<?php

namespace Kunstmaan\SitemapBundle\Tests\Model;

use Kunstmaan\SitemapBundle\Model\SitemapUrl;
use PHPUnit\Framework\TestCase;

class SitemapUrlTest extends TestCase
{
    /**
     * @dataProvider priorityDataProvider
     */
    public function testPriorityValues(float $priority, bool $expectException)
    {
        if ($expectException) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(sprintf('A sitemap url priority can\'t be higher than 1 or below 0. Value given "%s"', $priority));
        }

        $obj = new SitemapUrl('example-url', new \DateTimeImmutable(), $priority);

        if (!$expectException) {
            $this->assertInstanceOf(SitemapUrl::class, $obj);
            $this->assertSame($priority, $obj->getPriority());
        }
    }

    public function priorityDataProvider(): \Iterator
    {
        yield [0.0, false];
        yield [-1.0, true];
        yield [1.0, false];
        yield [1.2, true];
    }
}
