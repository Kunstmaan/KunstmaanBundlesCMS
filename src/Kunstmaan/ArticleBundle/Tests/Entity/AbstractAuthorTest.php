<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use PHPUnit\Framework\TestCase;

class AbstractAuthorTest extends TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Instantiating the "Kunstmaan\ArticleBundle\Entity\AbstractAuthor" class is deprecated in KunstmaanArticleBundle 5.1 and will be made abstract in KunstmaanArticleBundle 6.0. Extend your implementation from this class instead.
     */
    public function testInstantiatingClassDeprecation()
    {
        new AbstractAuthor();
    }

    public function testGettersAndSetters()
    {
        $entity = new class() extends AbstractAuthor {
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
