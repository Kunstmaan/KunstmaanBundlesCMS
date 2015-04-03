<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PageBannerPagePartAdminType
 */
class PageBannerPagePartAdminType extends \Symfony\Component\Form\AbstractType
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

	$builder->add('title', 'text', array(
	    'required' => true
	));
	$builder->add('description', 'textarea', array(
	    'attr' => array('rows' => 4, 'cols' => 600),
	    'required' => false,
	));
	$builder->add('backgroundImage', 'media', array(
	    'pattern' => 'KunstmaanMediaBundle_chooser',
	    'mediatype' => 'image',
	    'required' => false,
	));
	$builder->add('buttonUrl', 'urlchooser', array(
	    'required' => false,
	));
	$builder->add('buttonText', 'text', array(
	    'required' => false,
	));
	$builder->add('buttonNewWindow', 'checkbox', array(
	    'required' => false,
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
	return 'pagebannerpageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\PageParts\PageBannerPagePart'
	));
    }
}
