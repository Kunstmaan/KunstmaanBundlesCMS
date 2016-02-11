<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

	$builder->add('title', TextType::class, array(
	    'required' => true
	));
	$builder->add('description', TextAreaType::class, array(
	    'attr' => array('rows' => 4, 'cols' => 600),
	    'required' => false,
	));
	$builder->add('backgroundImage', MediaType::class, array(
	    'pattern' => 'KunstmaanMediaBundle_chooser',
	    'mediatype' => 'image',
	    'required' => false,
	));
	$builder->add('buttonUrl', UrlChooserType::class, array(
	    'required' => false,
	));
	$builder->add('buttonText', TextType::class, array(
	    'required' => false,
	));
	$builder->add('buttonNewWindow', CheckboxType::class, array(
	    'required' => false,
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
	return 'pagebannerpageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\PageParts\PageBannerPagePart'
	));
    }
}
