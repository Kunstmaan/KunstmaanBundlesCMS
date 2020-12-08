<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\MediaTokenTransformer;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilder;

class WysiwygTypeTest extends TestCase
{
    public function testMethods()
    {
        $mediaTokenTransformer = $this->createMock(MediaTokenTransformer::class);
        $wysiwygType = new WysiwygType($mediaTokenTransformer);

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())
            ->method('addModelTransformer')
            ->willReturn(true);

        /* @var FormBuilder $builder */
        $wysiwygType->buildForm($builder, []);

        $this->assertEquals(TextareaType::class, $wysiwygType->getParent());
        $this->assertEquals('wysiwyg', $wysiwygType->getBlockPrefix());
    }
}
