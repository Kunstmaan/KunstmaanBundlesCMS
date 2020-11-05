<?php

namespace Kunstmaan\NodeSearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeSearchAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('boost', TextType::class, [
            'label' => 'node_search.form.search.boost.label',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'node_search';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Kunstmaan\NodeSearchBundle\Entity\NodeSearch',
            ]
        );
    }
}
