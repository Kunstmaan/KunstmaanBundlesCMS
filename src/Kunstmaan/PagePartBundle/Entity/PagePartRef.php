<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reference between a page and a pagepart
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\PagePartBundle\Repository\PagePartRefRepository")
 * @ORM\Table(name="kuma_page_part_refs", indexes={@ORM\Index(name="idx_page_part_search", columns={"pageId", "pageEntityname", "context"})})
 * @ORM\HasLifecycleCallbacks()
 */
class PagePartRef
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $pageId;

    /**
     * @ORM\Column(type="string")
     */
    protected $pageEntityname;

    /**
     * @ORM\Column(type="string")
     */
    protected $context;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sequencenumber;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $pagePartId;

    /**
     * @ORM\Column(type="string")
     */
    protected $pagePartEntityname;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $num
     */
    public function setId($num)
    {
        $this->id = $num;
    }

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
    public function getPageEntityname()
    {
        return $this->pageEntityname;
    }

    /**
     * Set pageEntityname
     *
     * @param string $pageEntityname
     */
    public function setPageEntityname($pageEntityname)
    {
        $this->pageEntityname = $pageEntityname;
    }

    /**
     * get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Get sequencenumber
     *
     * @return int
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * Set sequencenumber
     *
     * @param int $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * Get pagePartId
     *
     * @return int
     */
    public function getPagePartId()
    {
        return $this->pagePartId;
    }

    /**
     * Set pagePartId
     *
     * @param string $pagePartId
     */
    public function setPagePartId($pagePartId)
    {
        $this->pagePartId = $pagePartId;
    }

    /**
     * Get pagePartEntityname
     *
     * @return string
     */
    public function getPagePartEntityname()
    {
        return $this->pagePartEntityname;
    }

    /**
     * Set pagePartEntityname
     *
     * @param string $pagePartEntityname
     */
    public function setPagePartEntityname($pagePartEntityname)
    {
        $this->pagePartEntityname = $pagePartEntityname;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'pagepartref in context ' . $this->getContext();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\PagePartBundle\Helper\PagePartInterface
     */
    public function getPagePart(EntityManager $em)
    {
        return $em->getRepository($this->getPagePartEntityname())->find($this->getPagePartId());
    }
}
