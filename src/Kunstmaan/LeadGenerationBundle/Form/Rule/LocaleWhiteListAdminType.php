<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class LocaleWhiteListAdminType extends AbstractRuleAdminType
{
    private $locales;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $locales = $domainConfiguration->getFrontendLocales();
        $this->locales = array_combine($locales, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', ChoiceType::class, [
            'label' => 'kuma_lead_generation.form.locale_white_list.locale.label',
            'attr' => [
                'info_text' => 'kuma_lead_generation.form.locale_white_list.locale.info_text',
            ],
            'choices' => $this->locales,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'locale_whitelist_form';
    }
}
