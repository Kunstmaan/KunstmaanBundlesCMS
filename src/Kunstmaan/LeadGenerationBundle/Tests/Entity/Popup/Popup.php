<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

class Popup extends AbstractPopup
{
    public function getControllerAction(): string
    {
        return null;
    }

    public function getAdminType(): string
    {
        return null;
    }
}
