<?php

namespace Kunstmaan\KMediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * class to define the form to upload a file
 *
 */
class GalleryType extends AbstractType
{
    protected $entityname;

    public function __construct($name)
    {
        $this->entityname = $name;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('parent', 'entity', array( 'class' => $this->getEntityName(), 'required' => false ))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_kmediabundle_imagegallerytype';
    }

    public function getEntityName()
    {
        return $this->entityname;
    }

}

?>