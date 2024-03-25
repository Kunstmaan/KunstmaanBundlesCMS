<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\ListWidget;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ListWidgetTest extends TestCase
{
    public function testWidget()
    {
        $view = new FakeView();
        $view->offsetSet('a', ['vars' => ['errors' => [new FormError('bang')]]]);

        $widget = $this->createMock(FormWidget::class);
        $builder = $this->createMock(FormBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);

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
        $this->assertEquals('@KunstmaanAdmin/FormWidgets/ListWidget/widget.html.twig', $listWidget->getTemplate());
        $this->assertCount(1, $listWidget->getExtraParams(new Request()));
    }
}
