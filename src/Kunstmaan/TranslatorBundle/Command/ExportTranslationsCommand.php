<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\ExportCommandHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class ExportTranslationsCommand extends ContainerAwareCommand
{
    /**
     * @var ExportCommandHandler
     */
    private $exportCommandHandler;

    /**
     * @param ExportCommandHandler|null $exportCommandHandler
     */
    public function __construct(/* ExportCommandHandler */ $exportCommandHandler = null)
    {
        parent::__construct();

        if (!$exportCommandHandler instanceof ExportCommandHandler) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $exportCommandHandler ? 'kuma:translator:export' : $exportCommandHandler);

            return;
        }

        $this->exportCommandHandler = $exportCommandHandler;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:translator:export')
            ->setDescription('Export stashed translations into files (gzip compressed)')
            ->addOption('domains', 'd', InputOption::VALUE_REQUIRED, 'Specify which domains to export, default all domains in the stash')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Specify which format files should be, default is yaml')
            ->addOption('locales', 'l', InputOption::VALUE_REQUIRED, 'Specifiy which locales to export, default all in the stash')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domains = $input->getOption('domains');
        $format = $input->getOption('format');
        $locales = $input->getOption('locales');

        if (null === $format) {
            throw new InvalidArgumentException('A format should be defined, e.g --format yml');
        }

        if (null === $this->exportCommandHandler) {
            $this->exportCommandHandler = $this->getContainer()->get('kunstmaan_translator.service.exporter.command_handler');
        }

        $exportCommand = new ExportCommand();
        $exportCommand
            ->setDomains($domains === null ? false : $domains)
            ->setFormat($format === null ? false : $format)
            ->setLocales($locales === null ? false : $locales);

        $this->exportCommandHandler->executeExportCommand($exportCommand);
    }
}
