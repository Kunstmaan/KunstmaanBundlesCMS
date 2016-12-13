<?php

namespace Kunstmaan\ApiBundle\Service\Transformers;

/**
 * Interface TransformerInterface
 */
interface TransformerInterface
{
    /**
     * This function will determine if the DataTransformer is eligible for transformation
     *
     * @param $object
     * @return bool
     */
    public function canTransform($object);

    public function transform($object);
}