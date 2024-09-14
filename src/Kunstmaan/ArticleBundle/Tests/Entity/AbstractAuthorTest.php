<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use PHPUnit\Framework\TestCase;

class AbstractAuthorTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new class extends AbstractAuthor {
        };
        $entity->setId(666);
        $entity->setLink('https://nasa.gov');
        $entity->setName('NASA');

        $this->assertEquals(666, $entity->getId());
        $this->assertEquals('https://nasa.gov', $entity->getLink());
        $this->assertEquals('NASA', $entity->getName());
        $this->assertEquals('NASA', $entity->__toString());
    }
}
