<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;

/**
 * Class IndexablePagePartsService
 *
 * Quick & dirty for now, needs to be in a generic PagePartService without static call &
 * without passing EntityManager as param...
 */
class IndexablePagePartsService
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns all indexable pageparts for the specified page and context
     *
     * @param HasPagePartsInterface $page
     * @param string                $context
     *
     * @return array
     */
    public function getIndexablePageParts(HasPagePartsInterface $page, $context = 'main')
    {
        $pageparts = $this->em
            ->getRepository('KunstmaanPagePartBundle:PagePartRef')
            ->getPageParts($page, $context);

        $indexablePageParts = array();
        foreach ($pageparts as $pagepart) {
            if ($pagepart instanceof IndexableInterface && !$pagepart->isIndexable()) {
                continue;
            }
            $indexablePageParts[] = $pagepart;
        }

        return $indexablePageParts;
    }
}
