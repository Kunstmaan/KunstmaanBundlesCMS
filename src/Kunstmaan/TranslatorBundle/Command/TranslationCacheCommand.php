<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class TranslationCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('kuma:translator:cache')
        ->setDescription('Request cache status and flush cache')
        ->addOption('flush',        'f',    InputOption::VALUE_NONE,        'Flush translation cache (if any)')
        ->addOption('status',      null,    InputOption::VALUE_NONE,    'Request cache status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('flush')) {
            return $this->flushTranslationCache($input, $output);
        } elseif ($input->getOption('status')) {
            return $this->showTranslationCacheStatus($input, $output);
        }

        throw new InvalidArgumentException('No or invalid option provided');
    }

    public function flushTranslationCache(InputInterface $input, OutputInterface $output)
    {
        if ( $this->getContainer()->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache() ) {
            $output->writeln('<info>Translation cache succesfully flushed</info>');
        }
    }

    public function showTranslationCacheStatus(InputInterface $input, OutputInterface $output)
    {
        $cacheValidator = $this->getContainer()->get('kunstmaan_translator.service.translator.cache_validator');
        $oldestFile = $cacheValidator->getOldestCachefileDate();
        $newestTranslation = $cacheValidator->getLastTranslationChangeDate();
        $isFresh = $cacheValidator->isCacheFresh();

        $output->writeln(sprintf('Oldest file mtime: <info>%s</info>', $oldestFile instanceof \DateTime ? $oldestFile->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Newest translation (in stash): <info>%s</info>', $newestTranslation instanceof \DateTime ? $newestTranslation->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Status: <info>%s</info>', $isFresh ? 'fresh' : 'outdated'));

        return 0;
    }
}
