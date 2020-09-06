<?php

namespace Kunstmaan\NodeBundle\Controller;

/**
 * @deprecated Using the "Kunstmaan\NodeBundle\Controller\SlugActionInterface" to customize the page render is deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Use the "kunstmaan_node.page_render" event instead.
 */
interface SlugActionInterface
{
    /**
     * @deprecated Using the "Kunstmaan\NodeBundle\Controller\SlugActionInterface::getControllerAction" to customize the page render is deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Use the "kunstmaan_node.page_render" event instead.
     *
     * @return mixed
     */
    public function getControllerAction();
}
