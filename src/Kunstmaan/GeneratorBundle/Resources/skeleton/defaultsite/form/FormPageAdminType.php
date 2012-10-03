<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormPageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface  $builder The builder
     * @param array                 $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('thanks', 'textarea', array('data_class' => '{{ namespace }}\Entity\FormPage', 'required' => false, 'attr' => array('class' => 'rich_editor')));
    }

    /**
     * @assert () == 'formpage'
     *
     * @return string
     */
    public function getName()
    {
        return 'formpage';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => '{{ namespace }}\Entity\FormPage'));
    }
}