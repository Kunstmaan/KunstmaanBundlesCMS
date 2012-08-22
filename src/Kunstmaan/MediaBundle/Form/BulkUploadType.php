<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class BulkUploadType extends AbstractType
{

    protected $accept;

    function __construct($accept = null)
    {
        $this->accept = $accept;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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