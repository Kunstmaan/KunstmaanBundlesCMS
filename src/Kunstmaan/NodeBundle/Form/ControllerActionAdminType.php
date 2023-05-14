<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ControllerActionAdminType
 */
class ControllerActionAdminType extends AbstractType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);
        $builder->add('title', TextType::class, [
            'label' => 'kuma_node.form.controller_action.title.label',
        ]);
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\NodeBundle\Entity\AbstractControllerAction',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'controller_action';
    }
}
