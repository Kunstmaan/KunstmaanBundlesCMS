<?php

namespace Tests\Kunstmaan\AdminBundle\Helper\FormWidgets;

use ArrayIterator;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class FakeView extends FormView
{
    /**
     * @param $name
     * @param $value
     */
    public function offsetSet($name, $value)
    {
        $this->children[$name] = $value;
    }
}

/**
 * Class FormWidgetTest
 * @package Tests\Kunstmaan\AdminBundle\Helper\FormWidgets
 */
class FormWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testWidget()
    {
        $views = new ArrayIterator();
        $views->vars = ['errors' =>[new FormError('bang')]];
        $view = new FakeView();
        $view->offsetSet('a', $views);

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('persist')->willReturn(new User());

        $widget = new FormWidget(['a' => 'b'], ['x' => 'y']);
        $widget->bindRequest(new Request());
        $widget->setIdentifier('id');
        $widget->persist($em);

        $this->assertEquals('KunstmaanAdminBundle:FormWidgets\FormWidget:widget.html.twig', $widget->getTemplate());
        $this->assertEquals('id', $widget->getIdentifier());
        $this->assertCount(1, $widget->getFormErrors($view));
        $this->assertCount(0, $widget->getExtraParams(new Request()));
        $this->assertCount(0, $widget->getOptions());
        $this->assertCount(1, $widget->getData());
        $this->assertCount(1, $widget->getTypes());

        $widget->addType('test', 'test');
        $this->assertCount(2, $widget->getTypes());
    }
}
