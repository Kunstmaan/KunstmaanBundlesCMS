<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use {{ namespace }}\Entity\PageParts\AbstractPagePart;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="kuma_{{ pagepartname }}_page_parts")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @return string
     */
    public function __toString()
    {
        return "{{ pagepart }}";
    }
}