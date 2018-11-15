<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'media.form.remote.name.label',
                    'constraints' => array(new NotBlank()),
                    'required' => true,
                )
            )
            ->add(
                'code',
                TextType::class,
                array(
                    'label' => 'media.form.remote.code.label',
                    'constraints' => array(new NotBlank()),
                    'required' => true,
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'label' => 'media.form.remote.type.label',
                    'choices' => array(),
                    'constraints' => array(new NotBlank()),
                    'required' => true,
                )
            )
            ->add(
                'copyright',
                TextType::class,
                array(
                    'label' => 'media.form.remote.copyright.label',
                    'required' => false,
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => 'media.form.remote.description.label',
                    'required' => false,
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
                        EntityType::class,
                        array(
                            'class' => 'KunstmaanMediaBundle:Folder',
                            'choice_label' => 'optionLabel',
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
