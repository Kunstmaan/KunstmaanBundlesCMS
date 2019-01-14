<?php

namespace Kunstmaan\RedirectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedirectAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['domainConfiguration']->isMultiDomainHost()) {
            $hosts = $options['domainConfiguration']->getHosts();
            $domains = array_combine($hosts, $hosts);
            $domains = array_merge(['redirect.all' => ''], $domains);

            $builder->add('domain', ChoiceType::class, [
                'label' => 'redirect.form.redirect.domain.label',
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ]);
        } else {
            $host = $options['domainConfiguration']->getHost();
            $builder->add('domain', HiddenType::class, [
                'data' => $host,
            ]);
        }

        $builder->add('origin', TextType::class, [
            'label' => 'redirect.form.redirect.origin.label',
            'required' => true,
            'attr' => [
                'info_text' => 'redirect.origin_info',
            ],
        ]);
        $builder->add('target', TextType::class, [
            'label' => 'redirect.form.redirect.target.label',
            'required' => true,
            'attr' => [
                'info_text' => 'redirect.target_info',
            ],
        ]);
        $builder->add('permanent', CheckboxType::class, [
            'label' => 'redirect.form.redirect.permanent.label',
            'required' => false,
        ]);
        $builder->add('note', TextType::class, [
            'label' => 'redirect.form.redirect.note.label',
            'required' => false,
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'domainConfiguration' => null,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'redirect_form';
    }
}
