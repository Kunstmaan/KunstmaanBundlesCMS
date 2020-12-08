<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets;

use ArrayIterator;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Form\ColorType;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class FormWidgetTest extends TestCase
{
    public function testWidget()
    {
        $views = new ArrayIterator();
        $views->vars = ['errors' => [new FormError('bang')]];
        $view = new FakeView();
        $view->offsetSet('a', $views);

        $builder = $this->createMock(FormBuilder::class);
        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('persist')->willReturn(new User());
        $builder->expects($this->once())->method('getData');
        $builder->expects($this->atLeastOnce())->method('add');
        $builder->expects($this->once())->method('setData');

        $widget = new FormWidget(['a' => new ColorType()], ['a' => 'data'], ['a' => ['options' => 'here']]);
        $widget->bindRequest(new Request());
        $widget->setIdentifier('id');
        $widget->persist($em);

        $this->assertEquals('@KunstmaanAdmin/FormWidgets/FormWidget/widget.html.twig', $widget->getTemplate());
        $this->assertEquals('id', $widget->getIdentifier());
        $this->assertCount(1, $widget->getFormErrors($view));
        $this->assertCount(0, $widget->getExtraParams(new Request()));
        $this->assertCount(1, $widget->getOptions());
        $this->assertCount(1, $widget->getData());
        $this->assertCount(1, $widget->getTypes());

        $widget->addType('test', new ColorType());
        $this->assertCount(2, $widget->getTypes());

        /* @var FormBuilderInterface $builder*/
        $widget->buildForm($builder);
    }
}
