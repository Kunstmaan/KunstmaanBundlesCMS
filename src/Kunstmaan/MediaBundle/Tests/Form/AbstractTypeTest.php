<?php

namespace Kunstmaan\MediaBundle\Tests\Form;

use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTypeTest extends WebTestCase
{
    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $factory;

    /**
     * @var OptionsResolver
     */
    protected $resolver;

    protected function setUp(): void
    {
        $formFactoryBuilderInterface = Forms::createFormFactoryBuilder();
        $formFactoryBuilderInterface->addType(new URLChooserType());
        $formFactoryBuilderInterface->addTypeGuesser(new DoctrineOrmTypeGuesser($this->createMock('Doctrine\Common\Persistence\ManagerRegistry')));
        $this->factory = $formFactoryBuilderInterface->getFormFactory();
        $this->dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
        $this->resolver = new OptionsResolver();
    }
}
