<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\ColorType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @group legacy
 */
class ColorTypeTest extends TestCase
{
    public function testMethods()
    {
        $colorType = new ColorType();
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())->method('setDefaults')->willReturn($resolver);
        /* @var OptionsResolver $resolver */
        $colorType->configureOptions($resolver);
        $this->assertSame(TextType::class, $colorType->getParent());
        $this->assertSame('color', $colorType->getBlockPrefix());
    }
}
