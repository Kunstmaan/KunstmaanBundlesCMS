<?php

namespace Kunstmaan\TaggingBundle\Entity;

interface LazyLoadingTaggableInterface extends Taggable
{
    public function setTagLoader(\Closure $loader);
}