<?php

namespace Kunstmaan\ArticleBundle\Tests\Form;

use Kunstmaan\ArticleBundle\Form\AbstractArticleOverviewPageAdminType;
use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractArticleOverviewPageAdminTypeTest
 */
class AbstractArticleOverviewPageAdminTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractArticlePageAdminType
     */
    private $object;

    public function setUp()
    {
        $entity = new AbstractArticleOverviewPageAdminType();
        $this->object = $entity;
    }

    public function testBlockPrefix()
    {
        $this->assertEquals('AbstractArticleOverviewPage', $this->object->getBlockPrefix());
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $this->object->configureOptions($resolver);
        $this->assertTrue($resolver->hasDefault('data_class'));
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->exactly(3))->method('add')->willReturn($builder);

        /* @var FormBuilder $builder */
        $this->object->buildForm($builder, []);
    }
}
