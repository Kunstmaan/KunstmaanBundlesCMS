<?php

namespace Kunstmaan\MediaBundle\Form\RemoteVideo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * RemoteVideoType
 */
class RemoteVideoType extends AbstractType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    'constraints' => array(new NotBlank()),
                    'required'    => true
                )
            )
            ->add(
                'code',
                'text',
                array(
                    'constraints' => array(new NotBlank()),
                    'required'    => true
                )
            )
            ->add(
                'type',
                'choice',
                array(
                    'choices'     => array('youtube' => 'youtube', 'vimeo' => 'vimeo', 'dailymotion' => 'dailymotion'),
                    'constraints' => array(new NotBlank()),
                    'required'    => true
                )
            )
            ->add(
                'copyright',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'required' => false
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
        return 'kunstmaan_mediabundle_videotype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper',
            )
        );
    }
}