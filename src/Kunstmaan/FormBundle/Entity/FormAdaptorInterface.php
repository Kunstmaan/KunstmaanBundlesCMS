<?php

namespace Kunstmaan\FormBundle\Entity;
use Symfony\Component\Form\FormBuilder;

/**
 * Form adaptor Interface
 */
interface FormAdaptorInterface
{

    /**
     * adapt the form here
     * @param FormBuilder $formBuilder The formbuilder
     * @param array       &$fields     The fields
     */
    public function adaptForm(FormBuilder $formBuilder, &$fields);

    /**
     * Returns a unique id
     * 
     * @return string
     */
    public function getUniqueId();

}
