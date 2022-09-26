<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\UspItem;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UspItemAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('icon', MediaType::class, [
            'mediatype' => 'image',
            'required' => true,
        ]);
        $builder->add('title', TextType::class, [
            'required' => true,
        ]);
        $builder->add('description', TextareaType::class, [
            'attr' => ['rows' => 4, 'cols' => 600],
            'required' => false,
        ]);
        $builder->add('weight', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UspItem::class,
        ]);
    }
}
