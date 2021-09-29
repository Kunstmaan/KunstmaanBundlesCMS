<?php

namespace Kunstmaan\NodeBundle\Controller;

/**
 * @deprecated Using the "Kunstmaan\NodeBundle\Controller\SlugActionInterface" to customize the page render is deprecated since KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Implement the "Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface" interface and provide a render service instead.
 */
interface SlugActionInterface
{
    /**
     * @deprecated Using the "Kunstmaan\NodeBundle\Controller\SlugActionInterface::getControllerAction" to customize the page render is deprecated since KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Implement the "Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface" interface and provide a render service instead.
     *
     * @return mixed
     */
    public function getControllerAction();
}
