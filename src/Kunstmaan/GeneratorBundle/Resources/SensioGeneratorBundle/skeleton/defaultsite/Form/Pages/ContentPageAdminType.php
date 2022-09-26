<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\ContentPage;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentPageAdminType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
{% if demosite %}
        $builder->add('menuImage', MediaType::class, [
            'mediatype' => 'image',
            'required' => false,
        ]);
        $builder->add('menuDescription', TextareaType::class, [
            'attr' => ['rows' => 3, 'cols' => 600],
            'required' => false,
        ]);
{% endif %}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentPage::class,
        ]);
    }
}
