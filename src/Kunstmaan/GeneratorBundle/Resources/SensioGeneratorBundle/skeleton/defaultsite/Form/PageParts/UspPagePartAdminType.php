<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use {{ namespace }}\Form\UspItemAdminType;

class UspPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('items', CollectionType::class, array(
	    'entry_type' => UspItemAdminType::class,
	    'allow_add' => true,
	    'allow_delete' => true,
	    'by_reference' => false,
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
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\PageParts\UspPagePart',
	));
    }


    public function getBlockPrefix()
    {
	return 'usppageparttype';
    }
}
