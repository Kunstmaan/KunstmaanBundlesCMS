<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\RangeType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @group legacy
 */
class RangeTypeTest extends TestCase
{
    public function testMethods()
    {
        $colorType = new RangeType();

        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())->method('setDefaults')->willReturn($resolver);

        /* @var OptionsResolver $resolver */
        $colorType->configureOptions($resolver);
        $this->assertEquals(IntegerType::class, $colorType->getParent());
        $this->assertEquals('range', $colorType->getBlockPrefix());
    }
}
