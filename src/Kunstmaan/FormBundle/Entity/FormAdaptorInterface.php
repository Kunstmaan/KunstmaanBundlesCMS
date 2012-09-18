<?php

namespace Kunstmaan\FormBundle\Entity;
use Symfony\Component\Form\FormBuilderInterface;
use ArrayObject;

/**
 * Form adaptor Interface
 */
interface FormAdaptorInterface
{

    /**
     * Adapt the form here
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields);

    /**
     * Returns a unique id
     *
     * @return string
     */
    public function getUniqueId();

}
