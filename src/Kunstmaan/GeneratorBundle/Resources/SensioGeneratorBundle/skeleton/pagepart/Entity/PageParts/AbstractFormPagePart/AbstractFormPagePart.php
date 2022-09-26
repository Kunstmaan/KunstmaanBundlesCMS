<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

abstract class {{ pagepart }} extends AbstractPagePart implements FormAdaptorInterface
{
    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="label", type="string")
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'label', type: 'string')]
{% endif %}
    protected $label;

    public function getUniqueId(): string
    {
        return str_replace('\\', '', ClassLookup::getClass($this)).$this->id;
    }

    /**
     * Set the label used for this page part.
     *
     * @param string $label
     *
     * @return AbstractFormPagePart
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label used for this page part.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getAdminView(): string
    {
        return 'PageParts/{{ pagepart }}/admin-view.html.twig';
    }
}
