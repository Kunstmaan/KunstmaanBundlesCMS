<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\BulkAction;

use Kunstmaan\AdminListBundle\AdminList\BulkAction\SimpleBulkAction;
use PHPUnit\Framework\TestCase;

class SimpleBulkActionTest extends TestCase
{
    /**
     * @var SimpleBulkAction
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SimpleBulkAction(['https://cia.gov'], 'CIA', 'fa-check', 'some.twig');
    }

    public function testGetters()
    {
        $this->assertCount(1, $this->object->getUrl());
        $this->assertSame('https://cia.gov', $this->object->getUrl()[0]);
        $this->assertSame('CIA', $this->object->getLabel());
        $this->assertSame('fa-check', $this->object->getIcon());
        $this->assertSame('some.twig', $this->object->getTemplate());
    }
}
