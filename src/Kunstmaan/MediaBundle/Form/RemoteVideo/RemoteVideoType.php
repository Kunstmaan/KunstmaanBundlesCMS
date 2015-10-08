<?php

namespace Kunstmaan\MediaBundle\Form\RemoteVideo;

use Kunstmaan\MediaBundle\Form\AbstractRemoteType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * RemoteVideoType
 */
class RemoteVideoType extends AbstractRemoteType
{

    /**
     * @var array
     */
    protected $configuration = array();

    /**
     * Constructor, gets the RemoteVideo configuration
     *
     * @param array $configuration
     */
    public function __construct($configuration = array())
    {
        $this->configuration = $configuration;
    }

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
                'choice',
                array(
                    'choices'     => $this->getRemoteVideoChoices(),
                    'constraints' => array(new NotBlank()),
                    'required'    => true
                )
            );
    }

    protected function getRemoteVideoChoices()
    {
        $choices = array();
        if (count($this->configuration)) {
            foreach ($this->configuration as $config => $enabled) {
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
    public function getName()
    {
        return 'kunstmaan_mediabundle_videotype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper',
            )
        );
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }
}
