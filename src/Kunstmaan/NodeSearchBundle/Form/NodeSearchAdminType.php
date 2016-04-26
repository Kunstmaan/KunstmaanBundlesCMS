<?php

namespace Kunstmaan\NodeSearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeSearchAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('boost', null, array(
            'label' => 'node_search.form.search.boost.label',
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'node_search';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Kunstmaan\NodeSearchBundle\Entity\NodeSearch',
            )
        );
    }
}
