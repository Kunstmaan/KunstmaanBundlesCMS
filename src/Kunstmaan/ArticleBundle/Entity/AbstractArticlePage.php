<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticlePagePagePartAdminConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\ArticleBundle\Repository\AbstractArticlePageRepository")
 * @ORM\Table(name="kuma_abstractarticles")
 */
class AbstractArticlePage extends AbstractPage implements HasPagePartsInterface {

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * Set the summary of this article
     * @param $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Returns the summary of this article
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array();
    }

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new AbstractArticlePageAdminType();
    }

    public function getAdminType()
    {
        return $this->getDefaultAdminType();
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new AbstractArticlePagePagePartAdminConfigurator());
    }
}