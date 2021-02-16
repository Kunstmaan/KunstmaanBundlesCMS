<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormHelper;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * The default tab implementation
 */
class Tab implements TabInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var FormWidget
     */
    protected $widget;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * @var string
     */
    protected $template;

    /**
     * @param string     $title  The title
     * @param FormWidget $widget The widget
     */
    public function __construct($title, FormWidget $widget)
    {
        $this->title = $title;
        $this->widget = $widget;

        $this->template = '@KunstmaanAdmin/Tabs/tab.html.twig';
    }

    /**
     * @return FormWidget
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return TabInterface
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $this->widget->buildForm($builder);
    }

    public function bindRequest(Request $request)
    {
        $this->widget->bindRequest($request);
    }

    public function persist(EntityManager $em)
    {
        $this->widget->persist($em);
    }

    /**
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        return $this->widget->getFormErrors($formView);
    }

    /**
     * @return FormHelper
     */
    protected function getFormHelper()
    {
        if (\is_null($this->formHelper)) {
            $this->formHelper = new FormHelper();
        }

        return $this->formHelper;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $identifier
     *
     * @return TabInterface
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        return $this->widget->getExtraParams($request);
    }
}
