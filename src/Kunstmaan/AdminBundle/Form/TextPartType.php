<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 *
 */
class TextPartType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array('required' => false, 'attr' => array('class' => 'rich_editor')));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_adminbundle_textparttype';
    }
}
