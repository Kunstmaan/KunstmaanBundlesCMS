<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FolderType extends AbstractType
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
        $folder = $options['folder'];
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'media.folder.addsub.form.name',
                ]
            )
            ->add(
                'rel',
                ChoiceType::class,
                [
                    'choices' => Folder::allTypes(),
                    'label' => 'media.folder.addsub.form.rel',
                ]
            )
            ->add(
                'parent',
                EntityType::class,
                [
                    'class' => 'KunstmaanMediaBundle:Folder',
                    'choice_label' => 'optionLabel',
                    'label' => 'media.folder.addsub.form.parent',
                    'required' => true,
                    'query_builder' => function (FolderRepository $er) use ($folder) {
                        return $er->selectFolderQueryBuilder($folder);
                    },
                ]
            )
            ->add(
                'internalName',
                TextType::class,
                [
                    'label' => 'media.folder.addsub.form.internal_name',
                    'required' => false,
                ]
            );
    }

    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_FolderType';
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
                'data_class' => 'Kunstmaan\MediaBundle\Entity\Folder',
                'folder' => null,
            ]
        );
    }
}
