<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Service\Transformers;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Kunstmaan\Rest\CoreBundle\Service\Transformers\TransformerInterface;
use Kunstmaan\Rest\NodeBundle\Model\ApiPage;
use Kunstmaan\Rest\NodeBundle\Model\ApiPagePart;

/**
 * Class PagePartTransformer
 */
class PagePartTransformer implements TransformerInterface
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * This function will determine if the DataTransformer is eligible for transformation
     *
     * @param $object
     * @return bool
     */
    public function canTransform($object)
    {
        return $object instanceof ApiPage;
    }

    /**
     * @param ApiPage $apiPage
     * @return ApiPage
     */
    public function transform($apiPage)
    {
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($apiPage->getPage(), 'main');

        $apiPageParts = new ArrayCollection();
        foreach ($pageparts as $pagepart) {
            $apiPagePart = new ApiPagePart();
            $apiPagePart->setContext('main');
            $apiPagePart->setPagePart($pagepart);
            $apiPageParts->add($apiPagePart);
        }
        $apiPage->setPageParts($apiPageParts);

        return $apiPage;
    }
}