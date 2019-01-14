<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

/**
 * RecopyPageTranslationNodeEvent
 */
class RecopyPageTranslationNodeEvent extends NodeEvent
{
    /**
     * @var NodeTranslation
     */
    private $originalNodeTranslation;

    /**
     * @var HasNodeInterface
     */
    private $originalPage;

    /**
     * @var string
     */
    private $originalLanguage;

    /**
     * @var NodeVersion
     */
    private $originalNodeVersion;

    /**
     * @param Node             $node                    The node
     * @param NodeTranslation  $nodeTranslation         The nodetranslation
     * @param NodeVersion      $nodeVersion             The node version
     * @param HasNodeInterface $page                    The object
     * @param NodeTranslation  $originalNodeTranslation The original node translation
     * @param NodeVersion      $originalNodeVersion     The original node version
     * @param HasNodeInterface $originalPage            The original page
     * @param string           $originalLanguage        The original language
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion, HasNodeInterface $page, NodeTranslation $originalNodeTranslation, NodeVersion $originalNodeVersion, HasNodeInterface $originalPage, $originalLanguage)
    {
        parent::__construct($node, $nodeTranslation, $nodeVersion, $page);
        $this->originalNodeTranslation = $originalNodeTranslation;
        $this->originalPage = $originalPage;
        $this->originalLanguage = $originalLanguage;
        $this->originalNodeVersion = $originalNodeVersion;
    }

    /**
     * @param string $originalLanguage
     *
     * @return RecopyPageTranslationNodeEvent
     */
    public function setOriginalLanguage($originalLanguage)
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalLanguage()
    {
        return $this->originalLanguage;
    }

    /**
     * @param NodeTranslation $originalNodeTranslation
     *
     * @return RecopyPageTranslationNodeEvent
     */
    public function setOriginalNodeTranslation($originalNodeTranslation)
    {
        $this->originalNodeTranslation = $originalNodeTranslation;

        return $this;
    }

    /**
     * @return NodeTranslation
     */
    public function getOriginalNodeTranslation()
    {
        return $this->originalNodeTranslation;
    }

    /**
     * @param HasNodeInterface $originalPage
     *
     * @return RecopyPageTranslationNodeEvent
     */
    public function setOriginalPage($originalPage)
    {
        $this->originalPage = $originalPage;

        return $this;
    }

    /**
     * @return HasNodeInterface
     */
    public function getOriginalPage()
    {
        return $this->originalPage;
    }

    /**
     * @param NodeVersion $originalNodeVersion
     *
     * @return RecopyPageTranslationNodeEvent
     */
    public function setOriginalNodeVersion($originalNodeVersion)
    {
        $this->originalNodeVersion = $originalNodeVersion;

        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getOriginalNodeVersion()
    {
        return $this->originalNodeVersion;
    }
}
