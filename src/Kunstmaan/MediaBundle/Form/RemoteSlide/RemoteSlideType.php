<?php

namespace Kunstmaan\MediaBundle\Form\RemoteSlide;

use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * RemoteSlideType
 */
class RemoteSlideType extends AbstractType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
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
                    'required' => true
                )
            )
            ->add(
                'code',
                'text',
                array(
                    'constraints' => array(new NotBlank()),
                    'required' => true
                )
            )
            ->add(
                'type',
                'choice',
                array(
                    'choices' => array('slideshare' => 'slideshare'),
                    'constraints' => array(new NotBlank()),
                    'required' => true
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $helper = $event->getData();
                $form = $event->getForm();

                // Make sure file field is when creating new (not persisted) objects
                if (null !== $helper->getMedia()->getId()) {
                    // Allow changing folder on edit
                    $form->add(
                        'folder',
                        'entity',
                        array(
                            'class' => 'KunstmaanMediaBundle:Folder',
                            'property' => 'optionLabel',
                            'query_builder' => function (FolderRepository $er) {
                                return $er->selectFolderQueryBuilder()
                                    ->andWhere('f.parent IS NOT NULL');
                            },
                            'required' => true,
                        )
                    );
                }
            }
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'kunstmaan_mediabundle_slidetype';
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
                'data_class' => 'Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHelper',
            )
        );
    }
}
