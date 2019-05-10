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
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            'pagepart.link.internal' => self::INTERNAL,
            'pagepart.link.external' => self::EXTERNAL,
            'pagepart.link.email' => self::EMAIL,
        ];

        if ($types = $options['link_types']) {
            foreach ($choices as $key => $choice) {
                if (!\in_array($choice, $types, false)) {
                    unset($choices[$key]);
                }
            }
        }

        $builder->add(
            'link_type',
            ChoiceType::class,
            [
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'js-change-link-type',
                ],
                'choices' => $choices,
            ]
        );

        $builder->get('link_type')->addEventSubscriber(new URLChooserLinkTypeSubscriber());

        $builder->addEventSubscriber(new URLChooserFormSubscriber());
        $builder->addViewTransformer(new URLChooserToLinkTransformer());
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
                'link_types' => [],
                'error_bubbling' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'urlchooser';
    }
}
