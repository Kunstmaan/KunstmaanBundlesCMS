<?php

namespace Kunstmaan\MediaBundle\Tests\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var FormFactoryInterface
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
        $formFactoryBuilderInterface->addTypeGuesser(new DoctrineOrmTypeGuesser($this->createMock(ManagerRegistry::class)));
        $this->factory = $formFactoryBuilderInterface->getFormFactory();
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
        $this->resolver = new OptionsResolver();
    }
}
