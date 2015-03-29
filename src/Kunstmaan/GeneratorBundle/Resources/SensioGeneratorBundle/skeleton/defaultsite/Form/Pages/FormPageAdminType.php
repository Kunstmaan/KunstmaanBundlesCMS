<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The admin type for form pages
 */
class FormPageAdminType extends PageAdminType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

	$builder->add('subject', 'text', array(
	    'required' => false,
	));
	$builder->add('fromEmail', 'email', array(
	    'required' => false,
	));
	$builder->add('toEmail', 'email', array(
	    'required' => false,
	));
        $builder->add('thanks', 'textarea', array(
	    'required' => false,
	    'attr' => array(
		'class' => 'js-rich-editor rich-editor'
	    )
        ));
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ namespace }}\Entity\Pages\FormPage'
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
