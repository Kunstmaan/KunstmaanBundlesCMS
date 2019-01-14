<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets;

use ArrayIterator;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\ListWidget;
use Kunstmaan\AdminBundle\Tests\unit\Helper\FormWidgets\FakeView;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FormWidgetTest
 */
class ListWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testWidget()
    {
        $views = new ArrayIterator();
        $views->vars = ['errors' => [new FormError('bang')]];
        $view = new FakeView();
        $view->offsetSet('a', $views);

        $widget = $this->createMock(FormWidget::class);
        $builder = $this->createMock(FormBuilder::class);
        $em = $this->createMock(EntityManager::class);

        $widget->expects($this->exactly(2))->method('bindRequest')->willReturn(true);
        $widget->expects($this->exactly(2))->method('persist')->willReturn(true);
        $widget->expects($this->exactly(2))->method('getFormErrors')->willReturn(['error' => 'argh']);
        $widget->expects($this->exactly(2))->method('getExtraParams')->willReturn(['x' => 'y']);
        $widget->expects($this->exactly(2))->method('buildForm')->willReturn(true);

        $listWidget = new ListWidget([$widget, clone $widget]);
        $this->assertCount(2, $listWidget->getWidgets());
        $this->assertInstanceOf(FormWidget::class, $listWidget->getWidgets()[0]);
        $this->assertInstanceOf(FormWidget::class, $listWidget->getWidgets()[1]);

        $listWidget->bindRequest(new Request());
        $listWidget->buildForm($builder);
        $listWidget->persist($em);

        $this->assertCount(1, $listWidget->getFormErrors($view));
        $this->assertEquals('KunstmaanAdminBundle:FormWidgets\ListWidget:widget.html.twig', $listWidget->getTemplate());
        $this->assertCount(1, $listWidget->getExtraParams(new Request()));
    }
}
