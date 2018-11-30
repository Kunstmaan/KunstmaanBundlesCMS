<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\unit\Entity\Popup;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

class Popup extends AbstractPopup
{
    public function getControllerAction()
    {
        return null;
    }

    public function getAdminType()
    {
        return null;
    }
}
