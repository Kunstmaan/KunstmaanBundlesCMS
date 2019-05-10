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
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence);

    /**
     * Returns a unique id
     *
     * @return string
     */
    public function getUniqueId();
}
