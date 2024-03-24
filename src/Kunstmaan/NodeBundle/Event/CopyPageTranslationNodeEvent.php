<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

final class CopyPageTranslationNodeEvent extends NodeEvent
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
     * @param NodeTranslation  $nodeTranslation         The node translation
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
     */
    public function setOriginalLanguage($originalLanguage): CopyPageTranslationNodeEvent
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    public function getOriginalLanguage(): string
    {
        return $this->originalLanguage;
    }

    /**
     * @param NodeTranslation $originalNodeTranslation
     */
    public function setOriginalNodeTranslation($originalNodeTranslation): CopyPageTranslationNodeEvent
    {
        $this->originalNodeTranslation = $originalNodeTranslation;

        return $this;
    }

    public function getOriginalNodeTranslation(): NodeTranslation
    {
        return $this->originalNodeTranslation;
    }

    /**
     * @param HasNodeInterface $originalPage
     */
    public function setOriginalPage($originalPage): CopyPageTranslationNodeEvent
    {
        $this->originalPage = $originalPage;

        return $this;
    }

    public function getOriginalPage(): HasNodeInterface
    {
        return $this->originalPage;
    }

    /**
     * @param NodeVersion $originalNodeVersion
     */
    public function setOriginalNodeVersion($originalNodeVersion): CopyPageTranslationNodeEvent
    {
        $this->originalNodeVersion = $originalNodeVersion;

        return $this;
    }

    public function getOriginalNodeVersion(): NodeVersion
    {
        return $this->originalNodeVersion;
    }
}
