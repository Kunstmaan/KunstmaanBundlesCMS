<?php

namespace Kunstmaan\MediaBundle\Form\RemoteVideo;

use Kunstmaan\MediaBundle\Form\AbstractRemoteType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RemoteVideoType extends AbstractRemoteType
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
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'label' => 'media.form.remote_video.type.label',
                    'choices' => $this->getRemoteVideoChoices($options['configuration']),
                    'constraints' => array(new NotBlank()),
                    'required' => true,
                )
            );
    }

    protected function getRemoteVideoChoices($configuration)
    {
        $choices = array();
        if (count($configuration)) {
            foreach ($configuration as $config => $enabled) {
                if (!$enabled) {
                    continue;
                }
                $choices[$config] = $config;
            }
        }

        return $choices;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_videotype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper',
                'configuration' => array(),
            )
        );
    }
}
