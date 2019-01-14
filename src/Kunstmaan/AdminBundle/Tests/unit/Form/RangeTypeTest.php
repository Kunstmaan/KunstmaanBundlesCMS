<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\RangeType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RangeTypeTest
 */
class RangeTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $colorType = new RangeType();

        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->willReturn(true);

        /* @var OptionsResolver $resolver */
        $colorType->configureOptions($resolver);
        $this->assertEquals(IntegerType::class, $colorType->getParent());
        $this->assertEquals('range', $colorType->getBlockPrefix());
    }
}
