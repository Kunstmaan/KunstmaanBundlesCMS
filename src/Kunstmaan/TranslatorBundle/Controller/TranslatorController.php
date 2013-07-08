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
        $cacheFresh = $this->container->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();

        if($this->container->getParameter('kernel.debug') === false && $cacheFresh === false) {
            $this->get('session')->getFlashBag()->add('warning', 'Rebuild cache to update to latest translations.');
        }

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
        $post = $this->getRequest()->request->all();
        $this->container->get('kunstmaan_translator.service.manager')->updateTranslationsFromArray($post['domain'], $post['translation']);
        $this->get('session')->getFlashBag()->add('success', 'Translations succesful saved!');
        return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $post['domain'])));
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

        $nbOfImports = $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);
        $this->get('session')->getFlashBag()->add('success', sprintf('%s translations imported', $nbOfImports));

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

        $this->get('session')->getFlashBag()->add('success', 'Translation cache flushed');
        return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    }
}
