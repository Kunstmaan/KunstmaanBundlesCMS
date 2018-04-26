<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Service\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\Rest\NodeBundle\Model\ApiContext;
use Kunstmaan\Rest\NodeBundle\Model\ApiPage;
use Kunstmaan\Rest\NodeBundle\Model\ApiPagePart;

/**
 * Class PagePartHelper
 */
class PagePartHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * PageTransformer constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param ApiPage         $apiPage
     * @param EntityInterface $page
     */
    public function updatePageParts(ApiPage $apiPage, EntityInterface $page)
    {
        /** @var PagePartRefRepository $ppRefRepo */
        $ppRefRepo = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');

        /** @var ApiContext $context */
        $newPageParts = [];
        foreach ($apiPage->getPageTemplate()->getContexts() as $context) {
            /** @var ApiPagePart $pagePart */
            foreach ($context->getPageParts() as $i => $pagePart) {
                if ($pagePart->getData()->getId()) {
                    $newPageParts[$pagePart->getData()->getId()] = $pagePart;
                } // No id, so new pagepart
                else {
                    $this->em->persist($pagePart->getData());
                    $this->em->flush($pagePart->getData());

                    $ppRefRepo->addPagePart($page, $pagePart->getData(), ($i + 1), $context->getName(), true);
                    $newPageParts[$pagePart->getData()->getId()] = $pagePart;
                }
            }

            $ppRefs = $ppRefRepo->getPagePartRefs($page, $context->getName());

            /** @var PagePartRef $ppRef */
            foreach ($ppRefs as $ppRef) {
                // If the pageparts does not exist anymore, remove it.
                if (!array_key_exists($ppRef->getPagePartId(), $newPageParts)) {
                    $pagePart = $ppRef->getPagePart($this->em);
                    $this->em->remove($ppRef);
                    $this->em->remove($pagePart);
                } else {
                    // If we switched pp positions, set correct sequence number now.
                    $i = array_search($ppRef->getPagePartId(), array_keys($newPageParts));
                    $ppRef->setSequencenumber($i);
                    $this->em->persist($ppRef);
                }
            }
        }
    }
}
