<?php

namespace Kunstmaan\ApiBundle\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\ApiBundle\Service\Transformers\TransformerInterface;

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
        $initialClass = get_class($object);
        foreach ($this->transformers as $transformer) {
            if (!$transformer->canTransform($object)) {
                continue;
            }

            $object = $transformer->transform($object);
        }

        if ($initialClass !== get_class($object)) {
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