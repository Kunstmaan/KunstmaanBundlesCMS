<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TranslatorCommandController extends AbstractController
{
    use TranslatorTrait;

    /**
     * @Route("/clear-cache", name="KunstmaanTranslatorBundle_command_clear_cache")
     */
    public function clearCacheAction()
    {

        $this->container->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache();
        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->getTranslator()->trans('kuma_translator.command.clear.flash.success')
        );

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
            ->setDefaultBundle($this->container->getParameter('kuma_translator.default_bundle'))
            ->setBundles($this->container->getParameter('kuma_translator.bundles'))
            ->setGlobals(true);

        $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->getTranslator()->trans('kuma_translator.command.import.flash.success')
        );

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
            ->setDefaultBundle($this->container->getParameter('kuma_translator.default_bundle'))
            ->setBundles($this->container->getParameter('kuma_translator.bundles'))
            ->setGlobals(false);

        $this->container->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->getTranslator()->trans('kuma_translator.command.import.flash.force_success')
        );

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }

    /**
     * @Route("/export", name="KunstmaanTranslatorBundle_command_export")
     */
    public function exportAction()
    {
        $locales = explode('|', $this->container->getParameter('requiredlocales'));
        $exportCommand = new ExportCommand();
        $exportCommand
            ->setLocales($locales)
            ->setFormat('csv')
            ->setDomains('messages');

        $response = $this->container->get('kunstmaan_translator.service.exporter.command_handler')->executeExportCSVCommand($exportCommand);

        return $response;
    }
}
