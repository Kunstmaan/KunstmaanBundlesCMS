<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        $builder->add('id', HiddenType::class);
        $builder->add('title', null, array(
            'label' => 'kuma_node.form.controller_action.title.label',
        ));
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
    public function getBlockPrefix()
    {
        return 'controller_action';
    }

}
