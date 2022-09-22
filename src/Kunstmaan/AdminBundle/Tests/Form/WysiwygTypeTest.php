<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\MediaTokenTransformer;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class WysiwygTypeTest extends TypeTestCase
{
    public function testMethods()
    {
        $mediaTokenTransformer = $this->createMock(MediaTokenTransformer::class);
        $wysiwygType = new WysiwygType($mediaTokenTransformer);

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->willReturn($builder);

        /* @var FormBuilder $builder */
        $wysiwygType->buildForm($builder, []);

        $this->assertEquals(TextareaType::class, $wysiwygType->getParent());
        $this->assertEquals('wysiwyg', $wysiwygType->getBlockPrefix());
    }

    public function testEditorModeOptionIsMissingByDefault()
    {
        $view = $this->factory->create(WysiwygType::class, null, [])->createView();

        $this->assertArrayNotHasKey('data-editor-mode', $view->vars['attr']);
    }

    public function testEditorModeOption()
    {
        $view = $this->factory->create(WysiwygType::class, null, ['editor-mode' => 'basic'])->createView();

        $this->assertSame('basic', $view->vars['attr']['data-editor-mode']);
    }

    protected function getExtensions(): array
    {
        $type = new WysiwygType($this->createMock(MediaTokenTransformer::class));

        return [
            new PreloadedExtension([$type], []),
        ];
    }
}
