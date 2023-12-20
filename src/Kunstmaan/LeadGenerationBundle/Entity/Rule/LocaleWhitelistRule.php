<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleWhiteListAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_locale_whitelist")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_rule_locale_whitelist')]
class LocaleWhitelistRule extends AbstractRule
{
    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="text", nullable=true)
     */
    #[ORM\Column(name: 'locale', type: 'text', nullable: true)]
    #[Assert\NotBlank]
    private $locale;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return LocaleWhitelistRule
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getJsObjectClass()
    {
        return 'LocaleWhitelistRule';
    }

    public function getJsProperties()
    {
        return [
            'locale' => $this->getLocale(),
        ];
    }

    /**
     * @return string
     */
    public function getService()
    {
        return 'kunstmaan_lead_generation.rule.service.localeruleservice';
    }

    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/LocaleWhitelistRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return LocaleWhiteListAdminType::class;
    }
}
