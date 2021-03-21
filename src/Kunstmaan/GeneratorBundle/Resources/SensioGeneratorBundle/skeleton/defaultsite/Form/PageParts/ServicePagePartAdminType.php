<?php

namespace {{ namespace }}\Form\PageParts;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use {{ namespace }}\Entity\PageParts\ServicePagePart;

/**
 * ServicePagePartAdminType
 */
class ServicePagePartAdminType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('title', TextType::class, array(
	    'required' => true,
	));
	$builder->add('description', WysiwygType::class, array(
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
