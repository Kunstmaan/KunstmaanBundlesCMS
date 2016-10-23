<?php

namespace Kunstmaan\ApiBundle\Service\Transformers;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\ApiBundle\Model\ApiPagePart;

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