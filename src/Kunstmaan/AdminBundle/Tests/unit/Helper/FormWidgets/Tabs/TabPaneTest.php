<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets\Tabs;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TabPaneTest
 */
class TabPaneTest extends PHPUnit_Framework_TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTabPane()
    {
        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(Request::class);
        $factory = $this->createMock(FormFactory::class);
        $builder = $this->createMock(FormBuilder::class);
        $form = $this->createMock(Form::class);
        $view = $this->createMock(FormView::class);
        $tab = $this->createMock(Tab::class);
        $tab2 = clone $tab;

        $request->expects($this->exactly(2))->method('get')->willReturn($tab);
        $tab->expects($this->any())->method('getExtraParams')->willReturn([1, 2, 3, 4, 5]);
        $tab->expects($this->any())->method('getTitle')->willReturn('Pass!');
        $form->expects($this->any())->method('createView')->willReturn($view);
        $form->expects($this->any())->method('isValid')->willReturn(true);
        $builder->expects($this->once())->method('getForm')->willReturn($form);
        $factory->expects($this->once())->method('createBuilder')->willReturn($builder);
        $em = $this->createMock(EntityManager::class);

        $tabPane = new TabPane('Title', $request, $factory);

        $tabPane->addTab($tab);
        $tabPane->addTab($tab2, 0);
        $tabPane->buildForm();
        $tabPane->bindRequest(new Request());
        $tabPane->persist($em);
        $tabPane->removeTab($tab2);
        $this->assertCount(5, $tabPane->getExtraParams(new Request()));
        $this->assertInstanceOf(Form::class, $tabPane->getForm());
        $this->assertInstanceOf(FormView::class, $tabPane->getFormView());
        $this->assertTrue($tabPane->isValid());
        $this->assertInstanceOf(Tab::class, $tabPane->getActiveTab());
        $this->assertNull($tabPane->getTabByPosition(5));
        $this->assertNotNull($tabPane->getTabByPosition(0));
        $this->assertNull($tabPane->getTabByTitle('Fail!'));
        $this->assertNotNull($tabPane->getTabByTitle('Pass!'));
        $tabPane->removeTabByTitle('Pass!');
        $tabPane->removeTabByTitle('not here');
        $this->assertEmpty($tabPane->getTabs());
        $tabPane->addTab($tab);
        $tabPane->addTab($tab2, 0);
        $tabPane->removeTabByPosition(0);
        $this->assertCount(1, $tabPane->getTabs());

        $request->request->expects($this->exactly(2))->method('get')->willReturn($tab);
        new TabPane('Title', $request, $factory);
    }
}
