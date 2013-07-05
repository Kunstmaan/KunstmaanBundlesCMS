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
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Use this field to give a name to your translator domain
     *
     * @var sting
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
