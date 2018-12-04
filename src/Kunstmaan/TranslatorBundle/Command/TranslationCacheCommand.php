<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class TranslationCacheCommand extends ContainerAwareCommand
{
    /**
     * @var ResourceCacher
     */
    private $resourceCacher;

    /**
     * @var CacheValidator
     */
    private $cacheValidator;

    /**
     * @param ResourceCacher|null $resourceCacher
     * @param CacheValidator|null $cacheValidator
     */
    public function __construct(/* ResourceCacher */ $resourceCacher = null, /* CacheValidator */ $cacheValidator = null)
    {
        parent::__construct();

        if (!$resourceCacher instanceof ResourceCacher) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $resourceCacher ? 'kuma:translator:cache' : $resourceCacher);

            return;
        }

        $this->resourceCacher = $resourceCacher;
        $this->cacheValidator = $cacheValidator;
    }

    protected function configure()
    {
        $this
        ->setName('kuma:translator:cache')
        ->setDescription('Request cache status and flush cache')
        ->addOption('flush', 'f', InputOption::VALUE_NONE, 'Flush translation cache (if any)')
        ->addOption('status', null, InputOption::VALUE_NONE, 'Request cache status')
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
        if (null === $this->resourceCacher) {
            $this->resourceCacher = $this->getContainer()->get('kunstmaan_translator.service.translator.resource_cacher');
        }

        if ($this->resourceCacher->flushCache()) {
            $output->writeln('<info>Translation cache succesfully flushed</info>');
        }
    }

    public function showTranslationCacheStatus(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->cacheValidator) {
            $this->cacheValidator = $this->getContainer()->get('kunstmaan_translator.service.translator.cache_validator');
        }

        $oldestFile = $this->cacheValidator->getOldestCachefileDate();
        $newestTranslation = $this->cacheValidator->getLastTranslationChangeDate();
        $isFresh = $this->cacheValidator->isCacheFresh();

        $output->writeln(sprintf('Oldest file mtime: <info>%s</info>', $oldestFile instanceof \DateTime ? $oldestFile->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Newest translation (in stash): <info>%s</info>', $newestTranslation instanceof \DateTime ? $newestTranslation->format('Y-m-d H:i:s') : 'none found'));
        $output->writeln(sprintf('Status: <info>%s</info>', $isFresh ? 'fresh' : 'outdated'));

        return 0;
    }
}
