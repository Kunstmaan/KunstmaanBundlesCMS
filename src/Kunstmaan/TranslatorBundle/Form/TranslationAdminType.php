<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intention = $options['csrf_token_id'];
        $options = [];
        if ($intention == 'edit') {
            $options = ['attr' => ['readonly' => true]];
        }

        $builder->add('domain', TextType::class, $options);
        $builder->add('keyword', TextType::class, $options);
        $builder->add('texts', CollectionType::class, [
            'entry_type' => TextWithLocaleAdminType::class,
            'label' => 'translator.translations',
            'by_reference' => false,
            'required' => false,
            'attr' => [
                'nested_form' => true,
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'translation';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\Kunstmaan\TranslatorBundle\Model\Translation',
        ]);
    }
}
