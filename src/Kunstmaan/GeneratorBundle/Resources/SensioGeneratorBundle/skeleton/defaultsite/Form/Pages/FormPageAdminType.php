<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\FormPage;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormPageAdminType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('subject', TextType::class, [
            'required' => false,
        ]);
        $builder->add('fromEmail', EmailType::class, [
            'required' => false,
        ]);
        $builder->add('toEmail', EmailType::class, [
            'required' => false,
        ]);
        $builder->add('thanks', WysiwygType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormPage::class,
        ]);
    }
}
