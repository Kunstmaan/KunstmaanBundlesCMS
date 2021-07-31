<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\Persistence\ManagerRegistry;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates all config files
 */
class ConfigGenerator extends KunstmaanGenerator
{
    /** @var bool */
    private $newAuthentication;

    public function __construct(Filesystem $filesystem, ManagerRegistry $registry, $skeletonDir, CommandAssistant $assistant, ContainerInterface $container = null, bool $newAuthentication = false)
    {
        parent::__construct($filesystem, $registry, $skeletonDir, $assistant, $container);
        $this->newAuthentication = $newAuthentication;
    }

    /**
     * Generate all config files.
     */
    public function generate(string $projectDir, bool $overwriteSecurity, bool $overwriteLiipImagine, bool $overwriteFosHttpCache)
    {
        $this->renderSingleFile(
            $this->skeletonDir,
            $projectDir . '/config/packages/',
            'security.yaml',
            [],
            true,
            $overwriteSecurity ? 'security.yaml' : 'security.yaml.example'
        );
        $this->renderSingleFile(
            $this->skeletonDir,
            $projectDir . '/config/packages/',
            'liip_imagine.yaml',
            [],
            true,
            $overwriteLiipImagine ? 'liip_imagine.yaml' : 'liip_imagine.yaml.example'
        );
        $this->renderSingleFile(
            $this->skeletonDir,
            $projectDir . '/config/packages/prod/',
            'fos_http_cache.yaml',
            [],
            true,
            $overwriteFosHttpCache ? 'fos_http_cache.yaml' : 'fos_http_cache.yaml.example'
        );
    }
}
