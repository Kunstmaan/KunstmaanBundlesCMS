<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Form\UspItemAdminType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class UspPagePartAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('items', 'collection', array(
	    'type' => new UspItemAdminType(),
	    'allow_add' => true,
	    'allow_delete' => true,
	    'by_reference' => false,
	    'cascade_validation' => true,
	    'attr' => array(
		'nested_form' => true,
		'nested_sortable' => true,
		'nested_form_min' => 1,
		'nested_form_max' => 3,
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
	    'data_class' => '\{{ namespace }}\Entity\PageParts\UspPagePart',
	    'cascade_validation' => true,
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
	return 'usppageparttype';
    }
}
