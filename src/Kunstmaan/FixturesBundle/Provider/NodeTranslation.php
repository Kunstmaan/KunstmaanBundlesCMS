<?php

namespace Kunstmaan\FixturesBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class NodeTranslation
 *
 * @package Kunstmaan\FixturesBundle\Provider
 */
class NodeTranslation
{
    private $nodeTransRepo;

    /**
     * NodeTranslation constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->nodeTransRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');
    }

    public function getTranslationByTitle($title, $lang)
    {
        return $this->nodeTransRepo->findOneBy(['title' => $title, 'lang' => $lang]);
    }
}
