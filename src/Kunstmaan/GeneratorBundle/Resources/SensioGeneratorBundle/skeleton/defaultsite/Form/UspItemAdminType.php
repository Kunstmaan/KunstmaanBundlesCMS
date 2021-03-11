<?php

namespace {{ namespace }}\Form;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UspItemAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('icon', MediaType::class, array(
	    'mediatype' => 'image',
	    'required' => true
	));
	$builder->add('title', TextType::class, array(
	    'required' => true
	));
	$builder->add('description', TextareaType::class, array(
	    'attr' => array('rows' => 4, 'cols' => 600),
	    'required' => false
	));
	$builder->add('weight', HiddenType::class, array(
	    'required' => true,
	));
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\UspItem'
	));
    }


    public function getBlockPrefix()
    {
	return 'uspitemtype';
    }
}
