<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This class represents the type for the SubmitButtonPagePart
 */
class SubmitButtonPagePartAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, array('required' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_singlelinetextpageparttype';
    }
}
