<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * A tab can be added to the TabPane and show fields or other information of a certain entity
 */
interface FormWidgetInterface
{
    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder);

    public function bindRequest(Request $request);

    /**
     * @param EntityManagerInterface $em The entity manager
     */
    public function persist(EntityManagerInterface $em);

    /**
     * @return array
     */
    public function getFormErrors(FormView $formView);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     *
     * @return TabInterface
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return array
     */
    public function getExtraParams(Request $request);
}
