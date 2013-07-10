<?php

namespace Kunstmaan\TranslatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that emulates a single symfony2 translations domain
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\TranslatorBundle\Repository\TranslationDomainRepository")
 * @ORM\Table(name="kuma_translation_domain")
 * @ORM\HasLifecycleCallbacks
 *
 * Future ideas
 * - priority rules, domain overrules files or opposite
 * - domain enabled or not
 *
 */
class TranslationDomain extends \Kunstmaan\TranslatorBundle\Model\Translation\TranslationDomain
{

    const FLAG_NEW = 'new';
    const FLAG_UPDATED = 'updated';

    /**
     * Use this field to give a name to your translator domain
     *
     * @var sting
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * A flag which defines the status of a specific domain ('updated', 'new', ..)
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $flag = null;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->flag = self::FLAG_NEW;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if ($this->flag == null) {
            $this->flag = self::FLAG_UPDATED;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFlag()
    {
        return $this->flag;
    }

    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    public function __toString()
    {
        return $this->name;
    }
}
