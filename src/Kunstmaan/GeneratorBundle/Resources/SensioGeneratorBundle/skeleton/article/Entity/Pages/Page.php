<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use {{ namespace }}\Form\Pages\{{ entity_class }}PageAdminType;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}PageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_pages")
 * @ORM\HasLifecycleCallbacks
 */
class {{ entity_class }}Page extends AbstractArticlePage implements HasPageTemplateInterface, SearchTypeInterface
{
    //%PagePartial.php.twig%

    public function __construct()
    {
        //%constructor%
    }

    //%PagePartialFunctions.php.twig%

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new {{ entity_class }}PageAdminType();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
	    return '{{ entity_class }}';
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
	    return array('{{ bundle.getName() }}:{{ entity_class|lower }}main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
	    return array('{{ bundle.getName() }}:{{ entity_class|lower }}page');
    }

    public function getDefaultView()
    {
	    return '{{ bundle.getName() }}:Pages/{{ entity_class }}Page:view.html.twig';
    }

    /**
     * Before persisting this entity, check the date.
     * When no date is present, fill in current date and time.
     *
     * @ORM\PrePersist
     */
    public function _prePersist()
    {
        // Set date to now when none is set
        if ($this->date == null) {
            $this->setDate(new \DateTime());
        }
    }
}
