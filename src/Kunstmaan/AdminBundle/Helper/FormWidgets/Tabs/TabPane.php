<?php
namespace Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A tab pane is a container which holds tabs
 */
class TabPane
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var TabInterface[]
     */
    protected $tabs = array();

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
     * @param string               $identifier  The identifier
     * @param Request              $request     The request
     * @param FormFactoryInterface $formFactory The form factory
     */
    public function __construct($identifier, Request $request, FormFactoryInterface $formFactory)
    {
        $this->identifier = $identifier;
        $this->formFactory = $formFactory;

        $this->slugifier = new Slugifier();
        if ($request->request->get('currenttab')) {
            $this->activeTab = $request->request->get('currenttab');
        } elseif ($request->get('currenttab')) {
            $this->activeTab = $request->get('currenttab');
        }
    }

    /**
     * @return FormInterface
     */
    public function buildForm()
    {
        $builder = $this->formFactory->createBuilder('form', null, array('cascade_validation'=>true));

        foreach ($this->tabs as $tab) {
            $tab->buildForm($builder);
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
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        foreach ($this->tabs as $tab) {
            $tab->persist($em);
        }
    }

    /**
     * @param TabInterface $tab
     *
     * @return string
     */
    private function generateIdentifier(TabInterface $tab)
    {
        return $this->slugifier->slugify($tab->getTitle());
    }

    /**
     * @param TabInterface $tab      The tab
     * @param null|int     $position The position
     *
     * @return TabPane
     */
    public function addTab(TabInterface $tab, $position = null)
    {
        $identifier = $tab->getIdentifier();
        if (!$identifier || empty($identifier)) {
            $tab->setIdentifier($this->generateIdentifier($tab));
        }

        if (!is_null($position) && is_numeric($position) && $position < sizeof($this->tabs)) {
            array_splice($this->tabs, $position, 0, array($tab));
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
            $this->reindexTabs();
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
                $this->reindexTabs();

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
        if (is_numeric($position) && $position < sizeof($this->tabs)) {
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
     * @param string $title
     *
     * @return TabInterface|null
     */
    public function getTabByTitle($title)
    {
        foreach ($this->tabs as $key => $tab) {
            if ($tab->getTitle() === $title) {
                return $this->tabs[$key];
            }
        }

        return null;
    }

    /**
     * @param int $position
     *
     * @return TabInterface|null
     */
    public function getTabByPosition($position)
    {
        if (is_numeric($position) && $position < sizeof($this->tabs)) {
            return $this->tabs[$position];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getActiveTab()
    {
        return !empty($this->activeTab) ? $this->activeTab : $this->tabs[0]->getIdentifier();
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

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->form->isValid();
    }

    /**
     * Reset the indexes of the tabs
     */
    private function reindexTabs()
    {
        $this->tabs = array_values($this->tabs);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $extraParams = array();
        foreach ($this->getTabs() as $tab) {
            $extraParams = array_merge($extraParams, $tab->getExtraParams($request));
        }

        return $extraParams;
    }
}
