<?php

namespace Kunstmaan\TaggingBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TaggingBundle\Entity\TagManager;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    protected $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public function transform($value)
    {
        $result = [];

        if (!($value instanceof ArrayCollection)) {
            return $result;
        }

        foreach ($value as $tag) {
            $result[] = $tag->getId();
        }

        return $result;
    }

    public function reverseTransform($value)
    {
        $result = new ArrayCollection();
        $manager = $this->tagManager;

        foreach ($value as $tagId) {
            $tag = $manager->findById((int) $tagId);
            $result->add($tag);
        }

        return $result;
    }
}
