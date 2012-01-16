<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 18/11/11
 * Time: 11:00
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\PagePartBundle\Repository\PagePartRefRepository")
 * @ORM\Table(name="pagepartref")
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


    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($num)
    {
        $this->id = $num;
    }

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
     * Set pageId
     *
     * @param string $refId
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
     * @return integer
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * Set sequencenumber
     *
     * @param integer $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * Get pagePartId
     *
     * @return integer
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
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
        $this->setUpdated(new \DateTime());
    }

    public function __toString()
    {
        return "pagepartref in context " . $this->getContext();
    }

    public function getDefaultAdminType(){
        return new PagePartRefAdminType();
    }
    
    public function getPagePart($em){
    	return $em->getRepository($this->getPagePartEntityname())->find($this->getPagePartId());
    }
}
