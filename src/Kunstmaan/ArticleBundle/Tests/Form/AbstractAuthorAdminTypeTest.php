<?php

namespace Kunstmaan\ArticleBundle\Tests\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class AbstractAuthorAdminTypeTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractAuthorAdminType();
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->exactly(2))->method('add')->willReturn($builder);
        $entity->buildForm($builder, []);

        $this->assertEquals('abstactauthor_form', $entity->getBlockPrefix());
    }
}
