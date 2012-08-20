<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * class to define the form to upload a picture
 *
 */
class TextPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array( 'required' => false, 'attr' => array( 'class' => 'rich_editor' )))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_adminbundle_textparttype';
    }
}