<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The admin type for content pages
 */
class ContentPageAdminType extends PageAdminType
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
{% if demosite %}
	$builder->add('menuImage', MediaType::class, array(
	    'mediatype' => 'image',
	    'required' => false
	));
	$builder->add('menuDescription', TextareaType::class, array(
	    'attr' => array('rows' => 3, 'cols' => 600),
	    'required' => false
	));
{% endif %}
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ namespace }}\Entity\Pages\ContentPage'
        ));
    }


    public function getBlockPrefix()
    {
	return 'contentpage';
    }
}
