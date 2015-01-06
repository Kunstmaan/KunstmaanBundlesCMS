<?php

namespace Kunstmaan\TranslatorBundle\Component\HttpKernel\DataCollector;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;

class TranslatorDataCollector extends DataCollector
{

    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

        $this->data = array(
            'translations' => $request->get('usedTranslations'),
        );
    }

    public function getTotalTranslations()
    {
        return count($this->data['translations']);
    }

    public function getTranslations()
    {
        $translationsCollection =  $this->data['translations'];

        if (!$translationsCollection instanceof \Doctrine\Common\Collections\ArrayCollection) {
            return new ArrayCollection;
        }

        $iterator = $translationsCollection->getIterator();

        $iterator->uasort(function (Translation $first, Translation $second) {
            return $first->getDomain() . $first->getKeyword() > $second->getDomain() . $second->getKeyword() ? 1 : -1;
        });

        return $iterator;
    }

    public function getName()
    {
        return 'translator';
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}
