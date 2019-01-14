<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\ColorType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColorTypeTest
 */
class ColorTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $colorType = new ColorType();
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->willReturn(true);
        /* @var OptionsResolver $resolver */
        $colorType->configureOptions($resolver);
        $this->assertEquals(TextType::class, $colorType->getParent());
        $this->assertEquals('color', $colorType->getBlockPrefix());
    }
}
