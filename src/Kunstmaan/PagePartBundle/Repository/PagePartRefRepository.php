<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
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
     * @param HasPagePartsInterface $page               The page
     * @param PagePartInterface     $pagepart           The pagepart
     * @param integer               $sequencenumber     The sequence numer
     * @param string                $context            The context
     * @param bool                  $pushOtherPageParts Push other pageparts (sequence + 1)
     *
     * @return \Kunstmaan\PagePartBundle\Entity\PagePartRef
     */
    public function addPagePart(HasPagePartsInterface $page, PagePartInterface $pagepart, $sequencenumber, $context = "main", $pushOtherPageParts = true)
    {
        if ($pushOtherPageParts) {
            $pagepartrefs = $this->getPagePartRefs($page);
            foreach ($pagepartrefs as $pagepartref) {
                if ($pagepartref->getSequencenumber() >= $sequencenumber) {
                    $pagepartref->setSequencenumber($pagepartref->getSequencenumber() + 1);
                    $this->getEntityManager()->persist($pagepartref);
                }
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
        return $this->findBy(array(
            'pageId' => $page->getId(),
            'pageEntityname' => ClassLookup::getClass($page),
            'context' => $context
        ), array('sequencenumber' => 'ASC'));
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

        // Group pagepartrefs per type and remember the sorting order
        $types = $order = array();
        $counter = 1;
        foreach ($pagepartrefs as $pagepartref) {
            $types[$pagepartref->getPagePartEntityname()][] = $pagepartref->getPagePartId();
            $order[$pagepartref->getPagePartEntityname() . $pagepartref->getPagePartId()] = $counter;
            $counter++;
        }

        // Fetch all the pageparts (only one query per pagepart type)
        $pageparts = array();
        foreach ($types as $classname => $ids) {
            $result = $this->getEntityManager()->getRepository($classname)->findBy(array('id' => $ids));
            $pageparts = array_merge($pageparts, $result);
        }

        // Order the pageparts
        usort($pageparts, function(EntityInterface $a, EntityInterface $b) use ($order) {
            $aPosition = $order[get_class($a) . $a->getId()];
            $bPosition = $order[get_class($b) . $b->getId()];

            if ($aPosition < $bPosition) {
                return -1;
            } elseif ($aPosition > $bPosition) {
                return 1;
            }
            return 0;
        });

        return $pageparts;
    }

    /**
     * @param EntityManager         $em       The entity manager
     * @param HasPagePartsInterface $fromPage The page from where you copy the pageparts
     * @param HasPagePartsInterface $toPage   The page to where you want to copy the pageparts
     * @param string                $context  The pagepart context
     */
    public function copyPageParts(EntityManager $em, HasPagePartsInterface $fromPage, HasPagePartsInterface $toPage, $context = "main")
    {
        $fromPageParts = $this->getPageParts($fromPage, $context);
        $sequenceNumber = 1;
        foreach ($fromPageParts as $fromPagePart) {
            $toPagePart = clone $fromPagePart;
            $toPagePart->setId(null);
            if ($toPagePart instanceof DeepCloneInterface) {
                $toPagePart->deepClone();
            }
            $em->persist($toPagePart);
            $em->flush($toPagePart);
            $this->addPagePart($toPage, $toPagePart, $sequenceNumber, $context, false);
            $sequenceNumber++;
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

    /**
     * @param bigint   $id             The id
     * @param string   $context        The context
     * @param int      $sequenceNumber The sequence number
     *
     * @return PagePart
     */
    public function getPagePart($id, $context = 'main', $sequenceNumber)
    {
        $ppRef = $this->find($id);
        $ppRef->setContext($context);
        $ppRef->setSequenceNumber($sequenceNumber);
        $this->getEntityManager()->persist($ppRef);
        $this->getEntityManager()->flush();

        return $this->getEntityManager()->getRepository($ppRef->getPagePartEntityName())->find($ppRef->getPagePartId());

    }
}
