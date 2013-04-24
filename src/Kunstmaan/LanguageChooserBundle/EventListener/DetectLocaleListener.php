<?php

namespace Kunstmaan\LanguageChooserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Lunetics\LocaleBundle\LocaleGuesser\LocaleGuesserManager;

/**
 * Listens for the languagechooser.detectlanguage and will use the Lunetics LocaleGuesserManager to
 * guess the language the user wants
 */
class DetectLocaleListener implements EventSubscriberInterface
{

    /**
     * Constructor, we need the LocaleGuesserManager
     *
     * @param LocaleGuesserManager $guesserManager Locale Guesser Manager
     */
    public function __construct(LocaleGuesserManager $guesserManager)
    {
        $this->guesserManager = $guesserManager;
    }


    public function onDetectLocaleEvent(GetResponseEvent $event)
    {
        $manager = $this->guesserManager;
        $request = $event->getRequest();

        $locale = $manager->runLocaleGuessing($request);
        if ($locale) {
            return $locale;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'languagechooser.detectlanguage' => array('onDetectLocaleEvent')
        );
    }


}