<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\UrlWhiteListAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_url_whitelist")
 */
class UrlWhitelistRule extends AbstractRule
{
    /**
     * @var string
     * @ORM\Column(name="urls", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $urls;

    /**
     * @return string
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @param string $urls
     *
     * @return UrlWhitelistRule
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsObjectClass()
    {
        return 'UrlWhitelistRule';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsProperties()
    {
        return array(
            'urls' => explode(PHP_EOL, $this->getUrls()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/UrlWhitelistRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return UrlWhiteListAdminType::class;
    }
}
