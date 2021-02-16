<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextWithLocaleAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', HiddenType::class, [
            'label' => 'kuma_translator.form.text_with_locale.locale.label',
        ]);
        $builder->add('text', TextareaType::class, [
            'label' => 'kuma_translator.form.text_with_locale.text.label',
            'required' => false,
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                // Reflect locale in label...
                $data = $event->getData();
                $form = $event->getForm();

                $options = $form->get('text')->getConfig()->getOptions();
                $options['label'] = strtoupper($data->getLocale());
                $form->add('text', TextareaType::class, $options);
            }
        );
    }

    public function getBlockPrefix()
    {
        return 'text_with_locale';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\Kunstmaan\TranslatorBundle\Model\TextWithLocale',
        ]);
    }
}
