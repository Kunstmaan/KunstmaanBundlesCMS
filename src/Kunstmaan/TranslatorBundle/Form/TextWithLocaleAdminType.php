<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextWithLocaleAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', 'hidden');
        $builder->add('text', 'textarea', array(
            'required' => false
        ));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                // Reflect locale in label...
                $data = $event->getData();
                $form = $event->getForm();

                $options = $form->get('text')->getConfig()->getOptions();
                $options['label'] = strtoupper($data->getLocale());
                $form->add('text', 'textarea', $options);
            }
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'text_with_locale';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Kunstmaan\TranslatorBundle\Model\TextWithLocale',
        ));
    }
}
