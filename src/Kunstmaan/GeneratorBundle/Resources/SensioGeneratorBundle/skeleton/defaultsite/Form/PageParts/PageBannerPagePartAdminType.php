<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\PageBannerPagePart;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageBannerPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'required' => true,
        ]);
        $builder->add('description', TextareaType::class, [
            'attr' => ['rows' => 4, 'cols' => 600],
            'required' => false,
        ]);
        $builder->add('backgroundImage', MediaType::class, [
            'mediatype' => 'image',
            'required' => false,
        ]);
        $builder->add('buttonUrl', URLChooserType::class, [
            'required' => false,
        ]);
        $builder->add('buttonText', TextType::class, [
            'required' => false,
        ]);
        $builder->add('buttonNewWindow', CheckboxType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageBannerPagePart::class,
        ]);
    }
}
