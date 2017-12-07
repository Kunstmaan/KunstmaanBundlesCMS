<?php

namespace Tests\Kunstmaan\LeadGenerationBundle\Entity\Popup;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

class Popup extends AbstractPopup
{
    protected function getControllerAction()
    {
        return null;
    }

    protected function getAdminType()
    {
        return null;
    }

}