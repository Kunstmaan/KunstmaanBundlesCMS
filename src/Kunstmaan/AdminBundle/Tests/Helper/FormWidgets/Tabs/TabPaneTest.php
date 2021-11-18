<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets\Tabs;

use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class TabPaneTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTabPane()
    {
        $factory = $this->createMock(FormFactory::class);
        $builder = $this->createMock(FormBuilder::class);
        $form = $this->createMock(Form::class);
        $view = $this->createMock(FormView::class);

        $tab = new Tab('properties', new FormWidget());

        $request = new Request([], ['currenttab' => $tab->getTitle()]);
        $form->expects($this->any())->method('createView')->willReturn($view);
        $form->expects($this->any())->method('isValid')->willReturn(true);
        $builder->expects($this->once())->method('getForm')->willReturn($form);
        $factory->expects($this->once())->method('createBuilder')->willReturn($builder);
        $tab2 = clone $tab;

        $tabPane = new TabPane('Title', $request, $factory);

        $tabPane->addTab($tab);
        $tabPane->addTab($tab2, 0);
        $tabPane->buildForm();
        $tabPane->bindRequest($request);
        $tabPane->removeTab($tab2);

        $this->assertInstanceOf(Form::class, $tabPane->getForm());
        $this->assertInstanceOf(FormView::class, $tabPane->getFormView());
        $this->assertTrue($tabPane->isValid());

        $this->assertSame('properties', $tabPane->getActiveTab());
        $this->assertNull($tabPane->getTabByPosition(5));
        $this->assertNotNull($tabPane->getTabByPosition(0));
        $this->assertNull($tabPane->getTabByTitle('Fail!'));
        $this->assertNotNull($tabPane->getTabByTitle('properties'));
        $tabPane->removeTabByTitle('properties');
        $tabPane->removeTabByTitle('not here');
        $this->assertEmpty($tabPane->getTabs());
        $tabPane->addTab($tab);
        $tabPane->addTab($tab2, 0);
        $tabPane->removeTabByPosition(0);
        $this->assertCount(1, $tabPane->getTabs());
    }
}
