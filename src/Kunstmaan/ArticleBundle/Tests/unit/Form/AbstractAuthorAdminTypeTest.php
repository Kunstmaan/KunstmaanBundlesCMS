<?php

namespace Kunstmaan\ArticleBundle\Tests\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;

/**
 * Class AbstractAuthorAdminTypeTest
 */
class AbstractAuthorAdminTypeTest extends PHPUnit_Framework_TestCase
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
