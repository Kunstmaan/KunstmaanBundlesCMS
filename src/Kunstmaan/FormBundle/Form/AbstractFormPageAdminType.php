<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * An abstract Form Page Admin Type
 */
class AbstractFormPageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
	$builder->add('thanks', 'textarea', array('required' => false, 'attr' => array('class' => 'js-rich-editor rich-editor')));
        $builder->add('subject');
        $builder->add('from_email');
        $builder->add('to_email');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\AbstractFormPage',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'formpage';
    }
}
