<?php

namespace Kunstmaan\FormBundle\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * An abstract Form Page Admin Type
 *
 * This should be an abstract class!
 */
class AbstractFormPageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'label' => 'kuma_form.form.page_admin.title.label',
        ));
        $builder->add('thanks', WysiwygType::class, array(
            'label' => 'kuma_form.form.page_admin.thanks.label',
            'required' => false,
        ));
        $builder->add('subject', TextType::class, array(
            'label' => 'kuma_form.form.page_admin.subject.label',
        ));
        $builder->add('from_email', EmailType::class, array(
            'label' => 'kuma_form.form.page_admin.from_email.label',
        ));
        $builder->add('to_email', TextType::class, array(
            'label' => 'kuma_form.form.page_admin.to_email.label',
        ));
    }

    /**
     *  This should also be abstract, it's impossible to instantiate this data_class!
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\AbstractFormPage',
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'formpage';
    }
}
