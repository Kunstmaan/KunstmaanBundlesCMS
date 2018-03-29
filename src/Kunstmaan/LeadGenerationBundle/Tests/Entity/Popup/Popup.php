<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup;

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