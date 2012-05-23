<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BulkUploadType extends AbstractType
{

    protected $accept;

    function __construct($accept = null)
    {
        $this->accept = $accept;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('files','file',array(
                          "required" => FALSE,
                          "attr" => array(
                              "accept" => $this->accept,
                              "multiple" => "multiple",
                          )
                     )
                );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return "kunstmaan_mediabundle_bulkupload";
    }
}