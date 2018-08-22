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

    /** @var [] */
    private $contexts;

    /**
     * IndexablePagePartsService constructor.
     *
     * @param EntityManagerInterface $em
     * @param []                     $contexts
     */
    public function __construct(EntityManagerInterface $em, $contexts = [])
    {
        $this->em = $em;
        $this->contexts = $contexts;
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
        $contexts = array_unique(array_merge($this->contexts, [$context]));

        $indexablePageParts = [];
        foreach ($contexts as $context) {
            $pageParts = $this->em
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->getPageParts($page, $context);

            foreach ($pageParts as $pagePart) {
                if ($pagePart instanceof IndexableInterface && !$pagePart->isIndexable()) {
                    continue;
                }
                $indexablePageParts[] = $pagePart;
            }
        }

        return $indexablePageParts;
    }
}
