<?php

namespace Kunstmaan\FixturesBundle\Provider;

use Doctrine\ORM\EntityManager;

class NodeTranslation
{
    private $nodeTransRepo;

    public function __construct(EntityManager $em)
    {
        $this->nodeTransRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');
    }

    public function getTranslationByTitle($title, $lang)
    {
        return $this->nodeTransRepo->findOneBy(array('title' => $title, 'lang' => $lang));
    }
}
