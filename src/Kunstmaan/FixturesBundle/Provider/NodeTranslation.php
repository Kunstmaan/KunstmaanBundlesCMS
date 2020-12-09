<?php

namespace Kunstmaan\FixturesBundle\Provider;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\NodeTranslation as NodeBundleNodeTranslation;

class NodeTranslation
{
    private $nodeTransRepo;

    public function __construct(EntityManager $em)
    {
        $this->nodeTransRepo = $em->getRepository(NodeBundleNodeTranslation::class);
    }

    public function getTranslationByTitle($title, $lang)
    {
        return $this->nodeTransRepo->findOneBy(['title' => $title, 'lang' => $lang]);
    }
}
