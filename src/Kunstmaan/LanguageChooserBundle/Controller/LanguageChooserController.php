<?php

namespace Kunstmaan\LanguageChooserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LanguageChooserController extends Controller
{
    /**
     * Handles the language chooser
     *
     * @return Response the request response
     */
    public function indexAction()
    {
        $enableAutodetect = $this->container->getParameter('kunstmaan_language_chooser.autodetectlanguage');
        $enableSplashpage = $this->container->getParameter('kunstmaan_language_chooser.showlanguagechooser');

        $defaultLocale    = $this->container->getParameter('defaultlocale');

        if ($enableAutodetect) {
            $localeGuesserManager   = $this->get('lunetics_locale.guesser_manager');

            $request                = $this->getRequest();

            $locale = $localeGuesserManager->runLocaleGuessing($request);

            if ($locale) {
                //locale returned will in the form of en_US, we need en
                $locale = locale_get_primary_language($locale);
            }

            // locale has been found, redirect
            if ($locale) {
                return $this->redirect($this->generateUrl('_slug', array('_locale' => $locale)), 302);
            } else {
                // no locale could be guessed, if splashpage is not enabled fallback to default and redirect
                if (!$enableSplashpage) {
                    return $this->redirect($this->generateUrl('_slug', array('_locale' => $defaultLocale)), 302);
                }
            }
        }

        if ($enableSplashpage) {
            $viewPath = $this->container->getParameter('kunstmaan_language_chooser.languagechoosertemplate');

            return $this->render($viewPath);
        }
    }
}
