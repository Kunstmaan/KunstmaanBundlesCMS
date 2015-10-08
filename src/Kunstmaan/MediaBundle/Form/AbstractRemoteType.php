<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * AbstractRemoteType
 */
abstract class AbstractRemoteType extends AbstractType
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
                    'choices' => array(),
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
}
