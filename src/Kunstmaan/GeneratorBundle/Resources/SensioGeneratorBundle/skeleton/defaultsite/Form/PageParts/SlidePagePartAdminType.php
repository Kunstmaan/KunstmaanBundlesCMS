<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SlidePagePartAdminType
 */
class SlidePagePartAdminType extends AbstractType
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
        $builder->add(
            'title',
            'text',
            array(
                'required' => false,
            )
        );
        $builder->add(
            'description',
            'textarea',
            array(
                'attr' => array('rows' => 5, 'cols' => 600),
                'required' => false,
            )
        );
        $builder->add(
            'image',
            'media',
            array(
                'pattern' => 'KunstmaanMediaBundle_chooser',
            )
        );
        $builder->add(
            'tickText',
            'text',
            array(
                'required' => false,
            )
        );
        $builder->add(
            'buttonUrl',
            'urlchooser',
            array(
                'required' => false,
            )
        );
        $builder->add(
            'buttonText',
            'text',
            array(
                'required' => false,
            )
        );
        $builder->add(
            'buttonNewWindow',
            'checkbox',
            array(
                'required' => false,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'slidepageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\{{ namespace }}\Entity\PageParts\SlidePagePart'
        ));
    }
}
