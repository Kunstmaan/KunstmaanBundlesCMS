<?php
/**
 * Created by PhpStorm.
 * User: ruud
 * Date: 23/10/2016
 * Time: 21:40
 */

namespace Kunstmaan\ApiBundle\Service\Transformers;


use Doctrine\ORM\EntityManager;
use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * Class PageTransformer
 */
class PageTransformer implements TransformerInterface
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
        return $object instanceof NodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     * @return ApiPage
     */
    public function transform($nodeTranslation)
    {
        $apiPage = new ApiPage();
        $apiPage->setNodeTranslation($nodeTranslation);
        $apiPage->setNode($nodeTranslation->getNode());
        $apiPage->setNodeVersion($nodeTranslation->getPublicNodeVersion());
        $page = $nodeTranslation->getRef($this->em);
        $apiPage->setPage($page);

        return $apiPage;
    }
}