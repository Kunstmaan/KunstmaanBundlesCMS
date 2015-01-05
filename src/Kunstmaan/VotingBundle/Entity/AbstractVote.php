<?php

namespace Kunstmaan\VotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract Vote class.
 *
 * The default value for a vote is 1 but can be overridden by setting the value before persisting.
 * Timestamp is automatically set to now when no timestamp has been set.
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class AbstractVote
{

    /**
     * Default value of any vote is 1
     *
     * @var integer
     */
    const DEFAULT_VALUE = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    /**
     * Use this to add your own identifier to which this vote is linked
     * Could be an integer (ID), string (URL, category, path, ...), or any other value for that matter
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $reference;

    /**
     * Use this field to add the ID of a meta entity containing meta information of this vote
     *
     * @var integer
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $meta;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ip;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $value;

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
     * Set id
     *
     * @param int $id The unique identifier
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setMeta($meta)
    {
        $this->meta;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @ORM\PrePersist
     */
    public function _prePersist()
    {
        // Set timestamp to now when none is set
        if ($this->timestamp === null) {
            $this->setTimestamp(new \DateTime('now'));
        }
        // Set $value to default value when value is null
        if ($this->value === null) {
            $this->value = $this::DEFAULT_VALUE;
        }
    }

}
