<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract ORM Pagepart
 */
abstract class AbstractPagePart extends AbstractEntity implements PagePartInterface
{

    /**
     * In most cases, the backend view will not differ from the default one.
     * Also, this implementation guarantees backwards compatibility.
     *
     * @return string
     */
    public function getAdminView()
    {
        return $this->getDefaultView();
    }

    /**
     * Use this method to override the default view for a specific page type.
     * Also, this implementation guarantees backwards compatibility.
     *
     * @return string
     */
    public function getOverrideView(PageInterface $page = null)
    {
        return $this->getDefaultView();
    }
}
