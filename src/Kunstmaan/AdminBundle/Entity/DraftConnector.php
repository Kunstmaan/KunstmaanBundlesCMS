<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\DraftConnectorRepository")
 * @ORM\Table(name="draftconnector")
 * @ORM\HasLifecycleCallbacks()
 */
class DraftConnector
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
    protected $publicId;

    /**
     * @ORM\Column(type="string")
     */
    protected $entityname;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $draftId;

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
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * Set pageId
     *
     * @param string $refId
     */
    public function setPublicId($id)
    {
        $this->publicId = $id;
    }

    /**
     * Get pageEntityname
     *
     * @return string
     */
    public function getEntityname()
    {
        return $this->entityname;
    }

    /**
     * Set pageEntityname
     *
     * @param string $pageEntityname
     */
    public function setEntityname($entityname)
    {
        $this->entityname = $entityname;
    }

    /**
     * Get draftId
     *
     * @return integer
     */
    public function getDraftId()
    {
        return $this->draftId;
    }

    /**
     * Set draftId
     *
     * @param integer $pagePartId
     */
    public function setDraftId($draftId)
    {
        $this->draftId = $draftId;
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
        return "draft of " . $this->getPageEntityname() . " with id " . $this->getPageId();
    }
}
