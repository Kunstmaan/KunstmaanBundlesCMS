<?php

namespace Kunstmaan\NodeBundle\Form\Type;

use Kunstmaan\NodeBundle\Form\DataTransformer\URLChooserToLinkTransformer;
use Kunstmaan\NodeBundle\Form\EventListener\URLChooserFormSubscriber;
use Kunstmaan\NodeBundle\Form\EventListener\URLChooserLinkTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * URLChooserType
 */
class URLChooserType extends AbstractType
{

    const INTERNAL = 'internal';
    const EXTERNAL = 'external';
    const EMAIL = 'email';

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['internal_link_only']) {
            $choices = [
                'pagepart.link.internal' => URLChooserType::INTERNAL,
                'pagepart.link.external' => URLChooserType::EXTERNAL,
                'pagepart.link.email' => URLChooserType::EMAIL,
            ];

            $builder->add('link_type', ChoiceType::class, array(
                'required' => true,
                'mapped' => false,
                'attr' => array(
                    'class' => 'js-change-link-type',
                ),
                'choices' => $choices,
            ));

            $builder->get('link_type')->addEventSubscriber(new URLChooserLinkTypeSubscriber());
        }

        $builder->addEventSubscriber(new URLChooserFormSubscriber());
        $builder->addViewTransformer(new URLChooserToLinkTransformer());
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'internal_link_only' => false
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'urlchooser';
    }
}
