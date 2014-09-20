<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TranslatorCommandController extends Controller
{


    /**
     * @Route("/clear-cache", name="KunstmaanTranslatorBundle_command_clear_cache")
     */
    public function clearCacheAction()
    {

        $this->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache();
        $this->get('session')->getFlashBag()->add('success', 'All live translations are up to date.');

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }

    /**
     * @Route("/import", name="KunstmaanTranslatorBundle_command_import")
     */
    public function importAction()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setBundle($this->container->getParameter('kuma_translator.default_bundle'))
            ->setGlobals(true);

        $this->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->get('session')->getFlashBag()->add('success', 'Translations successfully imported, none existing translations were overwritten.');

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }

    /**
     * @Route("/import-forced", name="KunstmaanTranslatorBundle_command_import_forced")
     */
    public function importForcedAction()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(true)
            ->setBundle($this->container->getParameter('kuma_translator.default_bundle'))
            ->setGlobals(false);

        $this->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->get('session')->getFlashBag()->add('success', 'Translations successfully imported, all existing translations were overwritten.');

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }


}
