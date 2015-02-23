<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use {{ namespace }}\Entity\PageParts\AbstractPagePart;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="kuma_{{ pagepartname }}_page_parts")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return {{ pagepart }}
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{{ pagepart }} " . $this->getContent();
    }
}