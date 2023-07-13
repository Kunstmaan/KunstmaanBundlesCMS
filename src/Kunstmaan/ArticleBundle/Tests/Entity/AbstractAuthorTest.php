<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use PHPUnit\Framework\TestCase;

class AbstractAuthorTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new class() extends AbstractAuthor {
        };
        $entity->setId(666);
        $entity->setLink('https://nasa.gov');
        $entity->setName('NASA');

        $this->assertSame(666, $entity->getId());
        $this->assertSame('https://nasa.gov', $entity->getLink());
        $this->assertSame('NASA', $entity->getName());
        $this->assertSame('NASA', $entity->__toString());
    }
}
