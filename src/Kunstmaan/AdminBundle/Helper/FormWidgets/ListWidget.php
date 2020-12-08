<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class ListWidget extends FormWidget
{
    /**
     * @var FormWidget[]
     */
    protected $widgets;

    /**
     * @param FormWidget[] $widgets
     */
    public function __construct(array $widgets = [])
    {
        parent::__construct([], []);
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
        return '@KunstmaanAdmin/FormWidgets/ListWidget/widget.html.twig';
    }

    /**
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = [];
        foreach ($this->widgets as $widget) {
            $params = array_merge($params, $widget->getExtraParams($request));
        }

        return $params;
    }
}
