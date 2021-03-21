<?php

namespace {{ namespace }}\Form\PageParts;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageBannerPagePartAdminType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('title', TextType::class, array(
	    'required' => true
	));
	$builder->add('description', TextareaType::class, array(
	    'attr' => array('rows' => 4, 'cols' => 600),
	    'required' => false,
	));
	$builder->add('backgroundImage', MediaType::class, array(
	    'mediatype' => 'image',
	    'required' => false,
	));
	$builder->add('buttonUrl', URLChooserType::class, array(
	    'required' => false,
	));
	$builder->add('buttonText', TextType::class, array(
	    'required' => false,
	));
	$builder->add('buttonNewWindow', CheckboxType::class, array(
	    'required' => false,
	));
    }


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
