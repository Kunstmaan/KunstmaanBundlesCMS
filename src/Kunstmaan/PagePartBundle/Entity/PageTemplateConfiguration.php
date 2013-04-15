<?php
namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\PageInterface;

/**
 * Configuration for page templates
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\PagePartBundle\Repository\PageTemplateConfigurationRepository")
 * @ORM\Table(name="kuma_page_template_configuration")
 */
class PageTemplateConfiguration extends AbstractEntity
{
    /**
     * @ORM\Column(type="bigint", name="page_id")
     */
    protected $pageId;

    /**
     * @ORM\Column(type="string", name="page_entity_name")
     */
    protected $pageEntityName;

    /**
     * @ORM\Column(type="string", name="page_template")
     */
    protected $pageTemplate;

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param integer $id
     */
    public function setPageId($id)
    {
        $this->pageId = $id;
    }

    /**
     * Get pageEntityname
     *
     * @return string
     */
    public function getPageEntityName()
    {
        return $this->pageEntityName;
    }

    /**
     * Set pageEntityname
     *
     * @param string $pageEntityName
     */
    public function setPageEntityName($pageEntityName)
    {
        $this->pageEntityName = $pageEntityName;
    }

    /**
     * get pageTemplate
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return $this->pageTemplate;
    }

    /**
     * Set pagetemplate
     *
     * @param string $pageTemplate
     */
    public function setPageTemplate($pageTemplate)
    {
        $this->pageTemplate = $pageTemplate;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return PageInterface
     */
    public function getPage(EntityManager $em)
    {
        return $em->getRepository($this->getPageEntityname())->find($this->getPageId());
    }
}
