<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Repository\PageTemplateConfigurationRepository;

/**
 * Configuration for page templates
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\PagePartBundle\Repository\PageTemplateConfigurationRepository")
 * @ORM\Table(name="kuma_page_template_configuration", indexes={@ORM\Index(name="idx_page_template_config_search", columns={"page_id", "page_entity_name"})})
 */
#[ORM\Entity(repositoryClass: PageTemplateConfigurationRepository::class)]
#[ORM\Table(name: 'kuma_page_template_configuration')]
#[ORM\Index(name: 'idx_page_template_config_search', columns: ['page_id', 'page_entity_name'])]
class PageTemplateConfiguration extends AbstractEntity
{
    /**
     * @ORM\Column(type="bigint", name="page_id")
     */
    #[ORM\Column(name: 'page_id', type: 'bigint')]
    protected $pageId;

    /**
     * @ORM\Column(type="string", name="page_entity_name")
     */
    #[ORM\Column(name: 'page_entity_name', type: 'string')]
    protected $pageEntityName;

    /**
     * @ORM\Column(type="string", name="page_template")
     */
    #[ORM\Column(name: 'page_template', type: 'string')]
    protected $pageTemplate;

    /**
     * Get pageId
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param int $id
     *
     * @return PageTemplateConfiguration
     */
    public function setPageId($id)
    {
        $this->pageId = $id;

        return $this;
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
     *
     * @return PageTemplateConfiguration
     */
    public function setPageEntityName($pageEntityName)
    {
        $this->pageEntityName = $pageEntityName;

        return $this;
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
     *
     * @return PageTemplateConfiguration
     */
    public function setPageTemplate($pageTemplate)
    {
        $this->pageTemplate = $pageTemplate;

        return $this;
    }

    /**
     * @return PageInterface
     */
    public function getPage(EntityManagerInterface $em)
    {
        return $em->getRepository($this->getPageEntityName())->find($this->getPageId());
    }
}
