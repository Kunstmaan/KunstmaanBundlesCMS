<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListWidget
 */
class ListWidget extends FormWidget
{
    /**
     * @var FormWidget[]
     */
    protected $widgets;

    /**
     * @param FormWidget[] $widgets
     */
    public function __construct(array $widgets = array())
    {
        parent::__construct(array(), array());
        $this->widgets = $widgets;
    }

    /**
     * @return FormWidget[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        foreach ($this->widgets as $widget) {
            $widget->buildForm($builder);
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        foreach ($this->widgets as $widget) {
            $widget->bindRequest($request);
        }
    }

    /**
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        foreach ($this->widgets as $widget) {
            $widget->persist($em);
        }
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = parent::getFormErrors($formView);

        foreach ($this->widgets as $widget) {
            $errors = array_merge($errors, $widget->getFormErrors($formView));
        }

        return $errors;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminBundle:FormWidgets\ListWidget:widget.html.twig';
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = array();
        foreach ($this->widgets as $widget) {
            $params = array_merge($params, $widget->getExtraParams($request));
        }

        return $params;
    }
}
