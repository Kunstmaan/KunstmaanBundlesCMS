<?php

namespace Users\Development\KunstmaanTranslatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslatorDomain class.
 *
 * This class emulates the translator domains used in Symfony2
 *
 * Possible to add:
 * - priority rules, domain overrules files or opposite
 * - domain enabled or not
 *
 */
class TranslatorDomain
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Use this field to give a name to your translator domain
     *
     * @var sting
     *
     * @ORM\Column(type="string")
     */
    private $name;

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