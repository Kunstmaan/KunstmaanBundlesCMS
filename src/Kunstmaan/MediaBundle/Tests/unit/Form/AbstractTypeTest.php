<?php

namespace Kunstmaan\MediaBundle\Tests\Form;

use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AbstractTypeTestCase
 */
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

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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
