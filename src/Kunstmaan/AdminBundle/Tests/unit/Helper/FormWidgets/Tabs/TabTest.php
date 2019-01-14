<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets\Tabs;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormHelper;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TabTest
 */
class TabTest extends PHPUnit_Framework_TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTab()
    {
        $em = $this->createMock(EntityManager::class);
        $builder = $this->createMock(FormBuilder::class);
        $view = $this->createMock(FormView::class);
        $widget = $this->createMock(FormWidget::class);
        $widget->expects($this->once())->method('getExtraParams')->willReturn([1, 2, 3, 4, 5]);
        $widget->expects($this->once())->method('getFormErrors')->willReturn([1, 2, 3, 4, 5]);
        $widget->expects($this->once())->method('buildForm')->willReturn(true);
        $widget->expects($this->once())->method('bindRequest')->willReturn(true);
        $widget->expects($this->once())->method('persist')->willReturn(true);

        $tab = new Tab('Title', $widget);
        $tab->buildForm($builder);
        $tab->bindRequest(new Request());
        $tab->persist($em);

        $tab->setIdentifier(666);
        $this->assertEquals('Title', $tab->getTitle());
        $tab->setTitle('Title 2');

        $this->assertEquals('KunstmaanAdminBundle:Tabs:tab.html.twig', $tab->getTemplate());
        $tab->setTemplate('new.twig');
        $this->assertEquals('new.twig', $tab->getTemplate());
        $this->assertEquals(666, $tab->getIdentifier());
        $this->assertEquals('Title 2', $tab->getTitle());
        $this->assertInstanceOf(FormWidget::class, $tab->getWidget());
        $this->assertCount(5, $tab->getExtraParams(new Request()));
        $this->assertCount(5, $tab->getFormErrors($view));

        $mirror = new ReflectionClass(Tab::class);
        $method = $mirror->getMethod('getFormHelper');
        $method->setAccessible(true);
        $helper = $method->invoke($tab);
        $this->assertInstanceOf(FormHelper::class, $helper);
    }
}
