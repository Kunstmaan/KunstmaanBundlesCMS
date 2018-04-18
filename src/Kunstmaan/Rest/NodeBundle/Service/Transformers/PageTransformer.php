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

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\Rest\CoreBundle\Service\Transformers\TransformerInterface;
use Kunstmaan\Rest\NodeBundle\Model\ApiEntity;
use Kunstmaan\Rest\NodeBundle\Model\ApiPage;

/**
 * Class PageTransformer
 */
class PageTransformer implements TransformerInterface
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
     * This function will determine if the DataTransformer is eligible for transformation
     *
     * @param $object
     *
     * @return bool
     */
    public function canTransform($object)
    {
        return $object instanceof NodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return ApiPage
     */
    public function transform($nodeTranslation)
    {
        $apiPage = new ApiPage();
        $apiPage->setNodeTranslation($nodeTranslation);
        $apiPage->setNode($nodeTranslation->getNode());
        $apiPage->setNodeVersion($nodeTranslation->getPublicNodeVersion());

        $page = $nodeTranslation->getRef($this->em);
        $apiEntity = new ApiEntity();
        $apiEntity->setData($page);

        $apiPage->setPage($apiEntity);

        return $apiPage;
    }
}
