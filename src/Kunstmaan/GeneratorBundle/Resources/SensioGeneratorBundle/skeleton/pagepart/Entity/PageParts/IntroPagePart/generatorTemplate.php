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
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @param string $linkText
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function __toString()
{
    return "{{ pagepart }}";
}
}