<?php

namespace Kunstmaan\RedirectBundle\Form;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class RedirectAdminType extends AbstractType
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

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
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $hosts = $this->domainConfiguration->getHosts();
            $domains = array_combine($hosts, $hosts);
            $domains = array_merge(array('redirect.all' => ''), $domains);

            $builder->add('domain', ChoiceType::class, array(
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices_as_values' => true,
            ));
        }

        $builder->add('origin', TextType::class, array(
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.origin_info'
            )
        ));
        $builder->add('target', TextType::class, array(
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.target_info'
            )
        ));
        $builder->add('permanent', CheckboxType::class, array(
            'required' => false
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
