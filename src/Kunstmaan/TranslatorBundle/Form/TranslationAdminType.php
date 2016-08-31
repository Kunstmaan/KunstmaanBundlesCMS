<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intention = $options['csrf_token_id'];
        $options = array();
        if ($intention == 'edit') {
            $options = array('attr' => array('readonly' => true));
        }

        $builder->add('domain', TextType::class, $options);
        $builder->add('keyword', TextType::class, $options);
        $builder->add('texts', CollectionType::class, array(
            'entry_type' => TextWithLocaleAdminType::class,
            'label' => 'translator.translations',
            'by_reference' => false,
            'required' => false,
            'attr' => array(
                'nested_form' => true,
            ),
        ));
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
        $resolver->setDefaults(array(
            'data_class' => '\Kunstmaan\TranslatorBundle\Model\Translation',
        ));
    }
}
