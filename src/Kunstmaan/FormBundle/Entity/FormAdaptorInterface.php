<?php

namespace Kunstmaan\FormBundle\Entity;

use ArrayObject;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form adaptor Interface
 */
interface FormAdaptorInterface
{

    /**
     * Modify the given FormBuilderInterface
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
