<?php

namespace Kunstmaan\RedirectBundle\Form;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
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
            $domains = array_merge(array('redirect.all' => ''), $domains);

            $builder->add('domain', ChoiceType::class, array(
                'label' => 'redirect.form.redirect.domain.label',
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false
            ));
        }

        $builder->add('origin', TextType::class, array(
            'label' => 'redirect.form.redirect.origin.label',
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.origin_info',
            ),
        ));
        $builder->add('target', TextType::class, array(
            'label' => 'redirect.form.redirect.target.label',
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.target_info',
            ),
        ));
        $builder->add('permanent', CheckboxType::class, array(
            'label' => 'redirect.form.redirect.permanent.label',
            'required' => false,
        ));
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'domainConfiguration' => null,
        ));
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
