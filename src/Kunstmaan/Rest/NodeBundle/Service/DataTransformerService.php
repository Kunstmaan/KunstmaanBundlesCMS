<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\Rest\NodeBundle\Service\Transformers\TransformerInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Class DataTransformerService
 */
class DataTransformerService
{
    /** @var TransformerInterface[] */
    private $transformers;

    public function __construct()
    {
        $this->transformers = new ArrayCollection();
    }

    /**
     * When transforming we will initialize a new transform when the object has changed
     * until the transformation stops
     *
     * @param $object
     */
    public function transform($object)
    {
        $initialClass = ClassLookup::getClass($object);
        foreach ($this->transformers as $transformer) {
            if (!$transformer->canTransform($object)) {
                continue;
            }

            $object = $transformer->transform($object);
        }

        if ($initialClass !== ClassLookup::getClass($object)) {
            $object = $this->transform($object);
        }

        return $object;
    }

    /**
     * Add transformers
     *
     * @param TransformerInterface $transformer
     * @return $this
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers->add($transformer);

        return $this;
    }

    /**
     * Get transformers
     *
     * @return TransformerInterface[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }
}