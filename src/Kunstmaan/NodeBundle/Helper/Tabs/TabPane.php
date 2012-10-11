<?php

namespace Kunstmaan\NodeBundle\Tabs;

use Doctrine\ORM\EntityManager;

use Kunstmaan\NodeBundle\Helper\Slugifier;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TabPane
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var TabInterface[]
     */
    protected $tabs;

    /**
     * @var string
     */
    protected $activeTab;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var FormView
     */
    protected $formView;

    /**
     * @param string  $identifier
     * @param Request $request
     */
    public function __construct($identifier, Request $request, FormFactoryInterface $formFactory)
    {
        $this->identifier = $identifier;
        $this->formFactory = $formFactory;

        if ($request->request->get('currenttab')) {
            $this->activeTab = $request->request->get('currenttab');
        } elseif ($request->get('currenttab')) {
            $this->activeTab = $request->get('currenttab');
        } else {
            $this->activeTab = 'pageparts1';
        }
    }

    /**
     * @return FormInterface
     */
    public function buildForm(Request $request)
    {
        $builder = $this->formFactory->createBuilder('form');

        foreach ($this->tabs as $tab) {
            $tab->buildForm($builder, $request);
        }

        $this->form = $builder->getForm();

        return $this->form;
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->form->bind($request);

        foreach ($this->tabs as $tab) {
            $tab->bindRequest($request);
        }
    }

    /**
     * @param EntityManager $em
     * @param Request       $request
     */
    public function persist(EntityManager $em, Request $request)
    {
        foreach ($this->tabs as $tab) {
            $tab->persist($em, $request);
        }
    }

    private function generateIdentifier(TabInterface $tab)
    {
        return Slugifier::slugify($tab->getTitle());
    }

    /**
     * @param TabInterface $tab
     * @param null|int     $position
     *
     * @return TabPane
     */
    public function addTab(TabInterface $tab, $position = null)
    {
        if (!$identifier = $tab->getIdentifier() || empty($identifier)) {
            $tab->setIdentifier($this->generateIdentifier($tab));
        }

        if (!is_null($position) && is_numeric($position) && $position < sizeof($this->tabs)) {
            array_splice($this->tabs, $position, 0, $tab);
        } else {
            $this->tabs[] = $tab;
        }

        return $this;
    }

    /**
     * @param TabInterface $tab
     *
     * @return TabPane
     */
    public function removeTab(TabInterface $tab)
    {
        if (in_array($tab, $this->tabs)) {
            unset($this->tabs[array_search($tab, $this->tabs)]);
        }

        return $this;
    }

    /**
     * @param string $title
     *
     * @return TabPane
     */
    public function removeTabByTitle($title)
    {
        foreach ($this->tabs as $key => $tab) {
            if ($tab->getTitle() === $title) {
                unset($this->tabs[$key]);

                return $this;
            }
        }

        return $this;
    }

    /**
     * @param int $position
     *
     * @return TabPane
     */
    public function removeTabByPosition($position)
    {
        if(is_numeric($position) && $position < sizeof($this->tabs)) {
            array_splice($this->tabs, $position, 1);
        }

        return $this;
    }

    /**
     * @return TabInterface[]
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * @return string
     */
    public function getActiveTab()
    {
        return $this->activeTab;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return FormView
     */
    public function getFormView()
    {
        if (is_null($this->formView)) {
            $this->formView = $this->form->createView();
        }

        return $this->formView;
    }

    public function isValid()
    {
        return $this->form->isValid();
    }

}
