<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\ServicePagePart;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * ServicePagePartAdminType
 */
class ServicePagePartAdminType extends \Symfony\Component\Form\AbstractType
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
	    'required' => true,
	));
	$builder->add('description', TextareaType::class, array(
	    'attr' => array('rows' => 10, 'cols' => 600, 'class' => 'js-rich-editor rich-editor', 'height' => 140),
	    'required' => false,
	));
	$builder->add('linkUrl', URLChooserType::class, array(
	    'required' => false,
	));
	$builder->add('linkText', TextType::class, array(
	    'required' => false,
	));
	$builder->add('linkNewWindow', CheckboxType::class, array(
	    'required' => false,
	));
	$builder->add('image', MediaType::class, array(
	    'mediatype' => 'image',
	    'required' => false,
	));
	$builder->add('imagePosition', ChoiceType::class, array(
	    'choices' => array_combine(ServicePagePart::$imagePositions, ServicePagePart::$imagePositions),
	    'placeholder' => false,
	    'required' => true,
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
	return 'servicepageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\PageParts\ServicePagePart'
	));
    }
}
