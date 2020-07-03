<?php

namespace Kunstmaan\MediaBundle\Form\File;

use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Kunstmaan\MediaBundle\Validator\Constraints\HasGuessableExtension;
use Kunstmaan\MediaBundle\Validator\Constraints\IsExtensionAllowed;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as BaseFileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * FileType
 */
class FileType extends AbstractType
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
        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'media.form.file.name.label',
                'required' => false,
            )
        );
        $builder->add(
            'file',
            BaseFileType::class,
            array(
                'label' => 'media.form.file.file.label',
                'constraints' => array(new File(), new HasGuessableExtension(), new IsExtensionAllowed()),
                'required' => false,
            )
        );
        $builder->add(
            'copyright',
            TextType::class,
            array(
                'label' => 'media.form.file.copyright.label',
                'required' => false,
            )
        );
        $builder->add(
            'description',
            TextareaType::class,
            array(
                'label' => 'media.form.file.description.label',
                'required' => false,
            )
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $helper = $event->getData();
                $form = $event->getForm();

                // Make sure file field is when creating new (not persisted) objects
                if (!$helper || null === $helper->getMedia()->getId()) {
                    $form->add(
                        'file',
                        BaseFileType::class,
                        array(
                            'label' => 'media.form.file.file.label',
                            'constraints' => array(new NotBlank(), new File(), new HasGuessableExtension(), new IsExtensionAllowed()),
                            'required' => true,
                        )
                    );
                } else {
                    // Display original filename only for persisted objects
                    $form->add(
                        'originalFilename',
                        TextType::class,
                        array(
                            'label' => 'media.form.file.originalFilename.label',
                            'required' => false,
                            'attr' => array(
                                'readonly' => 'readonly',
                            ),
                        )
                    );
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

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_filetype';
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
                'data_class' => 'Kunstmaan\MediaBundle\Helper\File\FileHelper',
            )
        );
    }
}
