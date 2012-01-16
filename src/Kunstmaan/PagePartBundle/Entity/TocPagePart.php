<?php

namespace  Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\TocPagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="tocpagepart")
 */
class TocPagePart {

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct() {
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    public function __toString(){
        return "TocPagePart";
    }

    public function getDefaultView(){
        return "KunstmaanPagePartBundle:TocPagePart:view.html.twig";
    }

    public function getDefaultAdminType(){
        return new TocPagePartAdminType();
    }
}