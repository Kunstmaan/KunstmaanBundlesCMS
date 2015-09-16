<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * AudioPagePartAdminType
 */
class AudioPagePartAdminType extends AbstractType
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
        $builder->add('media', 'media', array('pattern' => 'KunstmaanMediaBundle_chooser', 'mediatype' => 'audio', 'label' => 'mediapagepart.audio.choose'));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'kunstmaan_mediabundle_audiopageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\MediaPagePartBundle\Entity\AudioPagePart',
        ));
    }
}
