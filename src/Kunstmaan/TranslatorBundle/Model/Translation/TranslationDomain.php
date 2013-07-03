<?php
namespace Kunstmaan\TranslatorBundle\Model\Translation;

class TranslationDomain
{
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
