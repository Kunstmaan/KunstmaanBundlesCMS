<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

#[AsCommand(name: 'kuma:translator:cache', description: 'Request cache status and flush cache')]
final class TranslationCacheCommand extends Command
{
    /**
     * @var ResourceCacher
     */
    private $resourceCacher;

    /**
     * @var CacheValidator
     */
    private $cacheValidator;

    public function __construct(ResourceCacher $resourceCacher, CacheValidator $cacheValidator)
    {
        parent::__construct();

        $this->resourceCacher = $resourceCacher;
        $this->cacheValidator = $cacheValidator;
    }

    protected function configure(): void
    {
        $this
            ->addOption('flush', 'f', InputOption::VALUE_NONE, 'Flush translation cache (if any)')
            ->addOption('status', null, InputOption::VALUE_NONE, 'Request cache status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('flush')) {
            return $this->flushTranslationCache($input, $output);
        }

        if ($input->getOption('status')) {
            return $this->showTranslationCacheStatus($input, $output);
        }

        throw new InvalidArgumentException('No or invalid option provided');
    }

    public function flushTranslationCache(InputInterface $input, OutputInterface $output): int
    {
        if ($this->resourceCacher->flushCache()) {
            $output->writeln('<info>Translation cache succesfully flushed</info>');
        }

        return 0;
    }

    public function showTranslationCacheStatus(InputInterface $input, OutputInterface $output): int
    {
        $oldestFile = $this->cacheValidator->getOldestCachefileDate();
        $newestTranslation = $this->cacheValidator->getLastTranslationChangeDate();
        $isFresh = $this->cacheValidator->isCacheFresh();

        $output->writeln(sprintf('Oldest file mtime: <info>%s</info>', $oldestFile instanceof \DateTime ? $oldestFile->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Newest translation (in stash): <info>%s</info>', $newestTranslation instanceof \DateTime ? $newestTranslation->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Status: <info>%s</info>', $isFresh ? 'fresh' : 'outdated'));

        return 0;
    }
}
