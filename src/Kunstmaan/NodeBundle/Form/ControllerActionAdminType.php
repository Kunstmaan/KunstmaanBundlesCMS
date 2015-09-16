<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ControllerActionAdminType
 */
class ControllerActionAdminType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('title');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\NodeBundle\Entity\AbstractControllerAction',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'controller_action';
    }

}
