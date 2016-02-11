<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class LocaleBlackListAdminType extends AbstractRuleAdminType
{
    private $locales;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $locales = $domainConfiguration->getFrontendLocales();
        $this->locales = array_combine($locales, $locales);
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
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', ChoiceType::class, array(
            'attr'      => array('info_text' => 'Defines the locale that should be blacklisted'),
            'choices'   => $this->locales,
            'choices_as_values' => true
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'locale_blacklist_form';
    }
}
