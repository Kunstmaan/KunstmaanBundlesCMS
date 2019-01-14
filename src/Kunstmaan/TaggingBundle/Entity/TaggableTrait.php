<?php

namespace Kunstmaan\TaggingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Proxy\Proxy;

/**
 * @method getId
 */
trait TaggableTrait
{
    /**
     * @var Collection
     */
    private $tags;

    /**
     * @var \Closure
     */
    private $lazyTagLoader;

    /**
     * Returns the unique taggable resource type
     *
     * @return string
     */
    public function getTaggableType()
    {
        return ($this instanceof Proxy) ? get_parent_class($this) : get_class($this);
    }

    /**
     * Returns the unique taggable resource identifier
     *
     * @return string
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Returns the collection of tags for this Taggable entity
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $this->tags = new ArrayCollection();

            if ($this->lazyTagLoader) {
                call_user_func($this->lazyTagLoader, $this);
            }
        }

        return $this->tags;
    }

    public function setTags(Collection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function setTagLoader(\Closure $loader)
    {
        $this->lazyTagLoader = $loader;
    }
}
