<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Exception\InvalidDomainException;

class TranslatorController extends Controller
{
    /**
     * @Route("/show/{domain}", requirements={"domain"}, name="KunstmaanTranslatorBundle_translations_show")
     * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
     *
     * @return array
     */
    public function showAction($domain = false)
    {
        $this->validateDomain($domain);
        $this->validateCache();

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
     * @Route("/", requirements={"domain"}, name="KunstmaanTranslatorBundle_translations_index")
     * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->validateCache();
        $domains = $this->container->get('kunstmaan_translator.service.manager')->getAllDomains();

        return array(
                'translationGroups' => null,
                'managedLocales' => null,
                'domain' => null,
                'domains' => $domains
                );
    }

    /**
     * @Route("/save", name="KunstmaanTranslatorBundle_translations_save")
     * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
     *
     */
    public function saveAction()
    {
        $post = $this->getRequest()->request->all();

        try {

            $this->container->get('kunstmaan_translator.service.manager')->updateTranslationsFromArray($post['domain'], $post['translation']);

            $newTranslations = $post['translation_new'];

            // For now only add one at a time
            if (trim( (string) $newTranslations[0]['keyword']) != '') {
                $this->container->get('kunstmaan_translator.service.manager')->newTranslationsFromArray($newTranslations);
            }

            $this->get('session')->getFlashBag()->add('success', 'Translations succesful saved!');

            return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $post['domain'])));

        } catch (\Exception $e) {
            throw $e;
        }

        $data = $this->showAction($post['domain']);

        return $data;
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

        $force = false;

        if($this->getRequest()->get('force') == '1') {
            $force = true;
        }

        try {
            $importCommand = new ImportCommand();
            $importCommand
                ->setForce($force)
                ->setLocales(false)
                ->setGlobals(false)
                ->setBundle($this->container->getParameter('kuma_translator.default_bundle'));

            $nbOfImports = $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

            if ($nbOfImports <= 0) {
                $this->get('session')->getFlashBag()->add('warning', sprintf('No translations imported, because no new translation were found.', $nbOfImports));
            } else {
                $this->get('session')->getFlashBag()->add('success', sprintf('%s translations imported', $nbOfImports));
            }

            return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
        } catch (\Exception $e) {
            throw $e; // FIXME: do somehting useful
        }

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

    private function validateDomain($domainName)
    {

        if (trim($domainName) == '') {
            return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
        }

        $domain = $this->container->get('kunstmaan_translator.repository.translation_domain')->findOneByName($domainName);

        if (!$domain instanceOf \Kunstmaan\TranslatorBundle\Model\Translation\TranslationDomain) {
            throw new InvalidDomainException(sprintf('"%s" isn\'t a valid domain name.', $domainName));
        }
    }

    private function validateCache()
    {
        $cacheFresh = $this->container->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();

        if ($this->container->getParameter('kernel.debug') === false && $cacheFresh === false) {
            $this->get('session')->getFlashBag()->add('warning', 'Rebuild cache to update to latest translations.');
        }
    }
}
