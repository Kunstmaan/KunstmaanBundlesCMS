<?php

namespace Kunstmaan\TaggingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\TaggingBundle\Entity\TagManager;

class TagRepository extends EntityRepository
{
    protected $tagManager;

    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->tagManager = new TagManager($em, 'Kunstmaan\TaggingBundle\Entity\Tag', 'Kunstmaan\TaggingBundle\Entity\Tagging');
    }

    public function copyTags($origin, $destination)
    {
        $tagManager = $this->tagManager;
        $tags = $tagManager->getTagging($origin);
        $tagManager->replaceTags($tags, $destination);
        $tagManager->saveTagging($destination);
    }
}
