<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

/**
 * PagePartRefRepository
 */
class PagePartRefRepository extends EntityRepository
{

    /**
     * @param HasPagePartsInterface      $page           The page
     * @param PagePartInterface          $pagepart       The pagepart
     * @param integer                    $sequencenumber The sequence numer
     * @param string                     $context        The context
     *
     * @return \Kunstmaan\PagePartBundle\Entity\PagePartRef
     */
    public function addPagePart(HasPagePartsInterface $page, PagePartInterface $pagepart, $sequencenumber, $context = "main")
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
        $pageClassname = ClassLookup::getClass($page);
        $pagepartref->setPageEntityname($pageClassname);
        $pagepartref->setPageId($page->getId());
        $pagepartClassname = ClassLookup::getClass($pagepart);
        $pagepartref->setPagePartEntityname($pagepartClassname);
        $pagepartref->setPagePartId($pagepart->getId());
        $pagepartref->setSequencenumber($sequencenumber);
        $this->getEntityManager()->persist($pagepartref);
        $this->getEntityManager()->flush();

        return $pagepartref;
    }

    /**
     * @param HasPagePartsInterface $page    The page
     * @param string                $context The string
     *
     * @return PagePartRef[]
     */
    public function getPagePartRefs(HasPagePartsInterface $page, $context = "main")
    {
        return $this->findBy(array('pageId' => $page->getId(), 'pageEntityname' => ClassLookup::getClass($page), 'context' => $context), array('sequencenumber' => 'ASC'));
    }

    /**
     * @param HasPagePartsInterface $page    The page
     * @param string                $context The pagepart context
     *
     * @return PagePartInterface[]
     */
    public function getPageParts(HasPagePartsInterface $page, $context = "main")
    {
        $pagepartrefs = $this->getPagePartRefs($page, $context);
        $result = array();
        foreach ($pagepartrefs as $pagepartref) {
            $result[] = $pagepartref->getPagePart($this->getEntityManager());
        }

        return $result;
    }

    /**
     * @param EntityManager          $em       The entity manager
     * @param HasPagePartsInterface  $frompage The page from where you copy the pageparts
     * @param HasPagePartsInterface  $topage   The page to where you want to copy the pageparts
     * @param string                 $context  The pagepart context
     */
    public function copyPageParts(EntityManager $em, HasPagePartsInterface $frompage, HasPagePartsInterface $topage, $context = "main")
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
     * @param HasPagePartsInterface $page              The page
     * @param string                $pagepartClassname The classname of the pagepart
     * @param string                $context           The context
     *
     * @return mixed
     */
    public function countPagePartsOfType(HasPagePartsInterface $page, $pagepartClassname, $context = 'main')
    {
        $em = $this->getEntityManager();
        $pageClassname = ClassLookup::getClass($page);

        $sql = 'SELECT COUNT(pp.id) FROM KunstmaanPagePartBundle:PagePartRef pp
                 WHERE pp.pageEntityname = :pageEntityname
                   AND pp.pageId = :pageId
                   AND pp.pagePartEntityname = :pagePartEntityname
                   AND pp.context = :context';

        return $em->createQuery($sql)
                ->setParameter('pageEntityname', $pageClassname)
                ->setParameter('pageId', $page->getId())
                ->setParameter('pagePartEntityname', $pagepartClassname)
                ->setParameter('context', $context)->getSingleScalarResult();
    }

    /**
     * Test if entity has pageparts for the specified context
     *
     * @param HasPagePartsInterface $page    The page
     * @param string                $context The context
     *
     * @return bool
     */
    public function hasPageParts(HasPagePartsInterface $page, $context = 'main')
    {
        $em = $this->getEntityManager();
        $pageClassname = ClassLookup::getClass($page);

        $sql = 'SELECT COUNT(pp.id) FROM KunstmaanPagePartBundle:PagePartRef pp
                 WHERE pp.pageEntityname = :pageEntityname
                   AND pp.pageId = :pageId
                   AND pp.context = :context';

        return $em->createQuery($sql)
                ->setParameter('pageEntityname', $pageClassname)
                ->setParameter('pageId', $page->getId())
                ->setParameter('context', $context)->getSingleScalarResult() != 0;
    }
}
