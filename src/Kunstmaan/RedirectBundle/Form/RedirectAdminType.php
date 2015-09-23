<?php

namespace Kunstmaan\RedirectBundle\Form;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
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
            $domains = array_merge(array('' => 'redirect.all'), $domains);

            $builder->add('domain', 'choice', array(
                'choices' => $domains,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ));
        }

        $builder->add('origin', 'text', array(
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.origin_info'
            )
        ));
        $builder->add('target', 'text', array(
            'required' => true,
            'attr' => array(
                'info_text' => 'redirect.target_info'
            )
        ));
        $builder->add('permanent', 'checkbox', array(
            'required' => false
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'redirect_form';
    }
}
