<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\DashboardConfigurationType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;

/**
 * Class DashboardConfigurationTypeTest
 */
class DashboardConfigurationTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $type = new DashboardConfigurationType();

        $builder = $this->createMock(FormBuilder::class);

        $builder->expects($this->exactly(2))
            ->method('add')
            ->willReturn(true);

        /* @var FormBuilder $builder */
        $type->buildForm($builder, []);

        $this->assertEquals('dashboardconfiguration', $type->getBlockPrefix());
    }
}
