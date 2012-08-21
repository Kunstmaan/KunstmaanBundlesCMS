<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

class PagePartRefRepository extends EntityRepository
{

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage     $page
     * @param \Kunstmaan\PagePartBundle\Helper\PagePartInterface $pagepart
     * @param integer                                            $sequencenumber
     * @param string                                             $context
     *
     * @return \Kunstmaan\PagePartBundle\Entity\PagePartRef
     */
    public function addPagePart(AbstractPage $page, PagePartInterface $pagepart, $sequencenumber, $context = "main")
    {
        $pagepartrefs = $this->getPagePartRefs($page);
        foreach ($pagepartrefs as $pagepartref) {
            if ($pagepartref->getSequencenumber() >= $sequencenumber) {
                $pagepartref->setSequencenumber($pagepartref->getSequencenumber() + 1);
                $this->getEntityManager()->persist($pagepartref);
            }
        }
        $pagepartref = new \Kunstmaan\PagePartBundle\Entity\PagePartRef();
        $pagepartref->setContext($context);
        $page_classname = ClassLookup::getClass($page);
        $pagepartref->setPageEntityname($page_classname);
        $pagepartref->setPageId($page->getId());
        $pagepart_classname = ClassLookup::getClass($pagepart);
        $pagepartref->setPagePartEntityname($pagepart_classname);
        $pagepartref->setPagePartId($pagepart->getId());
        $pagepartref->setSequencenumber($sequencenumber);
        $this->getEntityManager()->persist($pagepartref);
        $this->getEntityManager()->flush();

        return $pagepartref;
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $context
     *
     * @return PagePartRef[]
     */
    public function getPagePartRefs(AbstractPage $page, $context = "main")
    {
        return $this->findBy(array('pageId' => $page->getId(), 'pageEntityname' => ClassLookup::getClass($page), 'context' => $context), array('sequencenumber' => 'ASC'));
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $context
     *
     * @return PagePartInterface[]
     */
    public function getPageParts(AbstractPage $page, $context = "main")
    {
        $pagepartrefs = $this->getPagePartRefs($page, $context);
        $result = array();
        foreach ($pagepartrefs as $pagepartref) {
            $result[] = $pagepartref->getPagePart($this->getEntityManager());
        }

        return $result;
    }

    /**
     * @param \Doctrine\ORM\EntityManager                    $em
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $frompage
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $topage
     * @param string                                         $context
     */
    public function copyPageParts(EntityManager $em, AbstractPage $frompage, AbstractPage $topage, $context = "main")
    {
        $frompageparts = $this->getPageParts($frompage, $context);
        $sequencenumber = 1;
        foreach ($frompageparts as $frompagepart) {
            $toppagepart = clone $frompagepart;
            $toppagepart->setId(null);
            $em->persist($toppagepart);
            $em->flush();
            $this->addPagePart($topage, $toppagepart, $sequencenumber, $context);
            $sequencenumber++;
        }
    }

    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $pagepart_classname
     * @param string                                         $context
     *
     * @return mixed
     */
    public function countPagePartsOfType(AbstractPage $page, $pagepart_classname, $context = 'main')
    {
        $em = $this->getEntityManager();
        $page_classname = ClassLookup::getClass($page);

        $sql = 'SELECT COUNT(pp.id) FROM KunstmaanPagePartBundle:PagePartRef pp
				 WHERE pp.pageEntityname = :pageEntityname
				   AND pp.pageId = :pageId
				   AND pp.pagePartEntityname = :pagePartEntityname
				   AND pp.context = :context';

        return $em->createQuery($sql)
                ->setParameter('pageEntityname', $page_classname)
                ->setParameter('pageId', $page->getId())
                ->setParameter('pagePartEntityname', $pagepart_classname)
                ->setParameter('context', $context)->getSingleScalarResult();
    }

    /**
     * Test if entity has pageparts for the specified context
     *
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param string                                         $context
     *
     * @return bool
     */
    public function hasPageParts(AbstractPage $page, $context = 'main')
    {
        $em = $this->getEntityManager();
        $page_classname = ClassLookup::getClass($page);

        $sql = 'SELECT COUNT(pp.id) FROM KunstmaanPagePartBundle:PagePartRef pp
				 WHERE pp.pageEntityname = :pageEntityname
				   AND pp.pageId = :pageId
				   AND pp.context = :context';

        return $em->createQuery($sql)
                ->setParameter('pageEntityname', $page_classname)
                ->setParameter('pageId', $page->getId())
                ->setParameter('context', $context)->getSingleScalarResult() != 0;
    }
}
