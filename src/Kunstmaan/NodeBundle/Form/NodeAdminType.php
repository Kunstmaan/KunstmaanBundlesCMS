<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * NodeAdminType
 */
class NodeAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'node';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\NodeBundle\Entity\Node',
        ]);
    }
}
