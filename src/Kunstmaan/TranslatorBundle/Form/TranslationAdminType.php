<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('domain', 'text');
        $builder->add('keyword', 'text');
        $builder->add(
          'texts',
          'collection',
          array(
            'type' => new TextWithLocaleAdminType(),
            'label' => 'translator.translations',
            'by_reference' => false,
            'required' => false,
            'attr' => array(
              'nested_form' => true,
            )
          )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translation';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
          array(
            'data_class' => '\Kunstmaan\TranslatorBundle\Model\Translation',
            'cascade_validation' => true,
          )
        );
    }
}
