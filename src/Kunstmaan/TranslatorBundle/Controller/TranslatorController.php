<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Exception\InvalidDomainException;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;

class TranslatorController extends AdminListController
{

    /**
     * @var AdminListConfiguratorInterface
     */
    private $adminListConfigurator;

    /**
     * @Route("/all", name="KunstmaanTranslatorBundle_translations")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function indexAction()
    {
        return parent::doIndexAction($this->getAdminListConfigurator());
    }

    /**
     * @Route("/all", name="KunstmaanTranslatorBundle_translations_show")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function showAction()
    {
        return parent::doIndexAction($this->getAdminListConfigurator());
    }

    /**
     * The add action
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_translations_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:add.html.twig")
     * @return array
     */
    public function addAction()
    {
        return parent::doAddAction($this->getAdminListConfigurator());
    }

    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     * @internal param $eid
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/edit/{domain}/{locale}/{keyword}", name="KunstmaanTranslatorBundle_translations_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:edit.html.twig")
     */
    public function editAction($domain, $locale, $keyword)
    {
        $configurator = $this->getAdminListConfigurator();
        $em = $this->getDoctrine()->getManager();

        $request = $this->getRequest();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneBy(array('domain' => $domain, 'locale' => $locale, 'keyword' => $keyword));
        if ($helper == null) {
            throw new NotFoundHttpException("Entity not found.");
        }
        $form = $this->createForm($configurator->getAdminType($helper), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();
                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
            }
        }

        $configurator->buildItemActions();

        return new Response($this->renderView($configurator->getEditTemplate(), array('form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator)));
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/delete/{domain}/{locale}/{keyword}", name="KunstmaanTranslatorBundle_translations_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction($id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id);
    }

    /**
     * @Route("/all", name="KunstmaanAdminBundle_settings_translations")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function settings()
    {
        return parent::doIndexAction($this->getAdminListConfigurator());
    }

    public function setAdminListConfigurator($adminListConfigurator)
    {
        $this->adminListConfigurator = $adminListConfigurator;
    }

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getManager());
        }
        return $this->adminListConfigurator;
    }


    // /**
    //  * @Route("/show/{domain}", requirements={"domain"}, name="KunstmaanTranslatorBundle_translations_show")
    //  * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
    //  *
    //  * @return array
    //  */
    // public function showoldAction($domain = false)
    // {
    //     $this->validateDomain($domain);
    //     $this->validateCache();

    //     $translationGroups = $this->container->get('kunstmaan_translator.service.manager')->getTranslationGroupsByDomain($domain);
    //     $managedLocales = $this->container->getParameter('kuma_translator.managed_locales');
    //     $domains = $this->container->get('kunstmaan_translator.service.manager')->getAllDomains();

    //     return array(
    //             'translationGroups' => $translationGroups,
    //             'managedLocales' => $managedLocales,
    //             'domain' => $domain,
    //             'domains' => $domains
    //             );
    // }

    // /**
    //  * @Route("/", requirements={"domain"}, name="KunstmaanTranslatorBundle_translations_index")
    //  * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
    //  *
    //  * @return array
    //  */
    // public function indexAction()
    // {
    //     $this->validateCache();
    //     $domains = $this->container->get('kunstmaan_translator.service.manager')->getAllDomains();

    //     return array(
    //             'translationGroups' => null,
    //             'managedLocales' => null,
    //             'domain' => null,
    //             'domains' => $domains
    //             );
    // }

    // /**
    //  * @Route("/save", name="KunstmaanTranslatorBundle_translations_save")
    //  * @Template("KunstmaanTranslatorBundle:Translator:index.html.twig")
    //  *
    //  */
    // public function saveAction()
    // {
    //     $post = $this->getRequest()->request->all();

    //     try {

    //         $this->container->get('kunstmaan_translator.service.manager')->updateTranslationsFromArray($post['domain'], $post['translation']);

    //         $newTranslations = $post['translation_new'];

    //         // For now only add one at a time
    //         if (trim( (string) $newTranslations[0]['keyword']) != '') {
    //             $this->container->get('kunstmaan_translator.service.manager')->newTranslationsFromArray($newTranslations);
    //         }

    //         $this->get('session')->getFlashBag()->add('success', 'Translations succesful saved!');

    //         return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $post['domain'])));

    //     } catch (\Exception $e) {
    //         throw $e;
    //     }

    //     $data = $this->showAction($post['domain']);

    //     return $data;
    // }

    // /**
    //  * @Route("/import/{bundle}", name="KunstmaanTranslatorBundle_translations_import_bundle")
    //  * @Template()
    //  *
    //  * @return array
    //  */
    // public function importAction($bundle = false)
    // {
    //     $domain = $this->container->get('kunstmaan_translator.service.manager')->getFirstDefaultDomainName();

    //     $force = false;

    //     if($this->getRequest()->get('force') == '1') {
    //         $force = true;
    //     }

    //     try {
    //         $importCommand = new ImportCommand();
    //         $importCommand
    //             ->setForce($force)
    //             ->setLocales(false)
    //             ->setGlobals(false)
    //             ->setBundle($this->container->getParameter('kuma_translator.default_bundle'));

    //         $nbOfImports = $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

    //         if ($nbOfImports <= 0) {
    //             $this->get('session')->getFlashBag()->add('warning', sprintf('No translations imported, because no new translation were found.', $nbOfImports));
    //         } else {
    //             $this->get('session')->getFlashBag()->add('success', sprintf('%s translations imported', $nbOfImports));
    //         }

    //         return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    //     } catch (\Exception $e) {
    //         throw $e; // FIXME: do somehting useful
    //     }

    // }

    // /**
    //  * @Route("/flush", name="KunstmaanTranslatorBundle_translations_flush_cache")
    //  * @Template()
    //  *
    //  * @return array
    //  */
    // public function flushCacheAction()
    // {
    //     $this->container->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache();
    //     $domain = $this->container->get('kunstmaan_translator.service.manager')->getFirstDefaultDomainName();

    //     $this->get('session')->getFlashBag()->add('success', 'Translation cache flushed');

    //     return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    // }

    // private function validateDomain($domainName)
    // {

    //     if (trim($domainName) == '') {
    //         return $this->redirect($this->generateUrl('KunstmaanTranslatorBundle_translations_show', array('domain' => $domain)));
    //     }

    //     $domain = $this->container->get('kunstmaan_translator.repository.translation_domain')->findOneByName($domainName);

    //     if (!$domain instanceOf \Kunstmaan\TranslatorBundle\Model\Translation\TranslationDomain) {
    //         throw new InvalidDomainException(sprintf('"%s" isn\'t a valid domain name.', $domainName));
    //     }
    // }

    // private function validateCache()
    // {
    //     $cacheFresh = $this->container->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();

    //     if ($this->container->getParameter('kernel.debug') === false && $cacheFresh === false) {
    //         $this->get('session')->getFlashBag()->add('warning', 'Rebuild cache to update to latest translations.');
    //     }
    // }
}
