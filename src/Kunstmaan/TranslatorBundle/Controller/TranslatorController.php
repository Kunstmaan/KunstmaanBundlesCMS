<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;

class TranslatorController extends Controller
{
    /**
     * @Route("/show/{domain}", requirements={"domain"}, name="KunstmaanTranslatorBundle_translations_show")
     * @Template()
     *
     * @return array
     */
    public function indexAction($domain = false)
    {
        $translationGroups = $this->container->get('kunstmaan_translator.service.manager')->getTranslationGroupsByDomain($domain);
        $managedLocales = $this->container->getParameter('kuma_translator.managed_locales');
        $domains = $this->container->get('kunstmaan_translator.service.manager')->getAllDomains();

        return array(
                'translationGroups' => $translationGroups,
                'managedLocales' => $managedLocales,
                'domain' => $domain,
                'domains' => $domains
                );
    }


    /**
     * @Route("/", name="KunstmaanTranslatorBundle_translations_save")
     * @Template()
     *
     */
    public function saveAction()
    {
        $domain = $this->getRequest()->get('domain');
        return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    }

    /**
     * @Route("/import/{bundle}", name="KunstmaanTranslatorBundle_translations_import_bundle")
     * @Template()
     *
     * @return array
     */
    public function importAction($bundle = false)
    {
        $domain = $this->container->get('kunstmaan_translator.service.manager')->getFirstDefaultDomainName();

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocale(false)
            ->setGlobals(false)
            ->setBundle($this->container->getParameter('kuma_translator.default_bundle'));

        $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);
        return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    }

    /**
     * @Route("/flush", name="KunstmaanTranslatorBundle_translations_flush_cache")
     * @Template()
     *
     * @return array
     */
    public function flushCacheAction()
    {
        $this->container->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache();
        $domain = $this->container->get('kunstmaan_translator.service.manager')->getFirstDefaultDomainName();
        return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    }
}