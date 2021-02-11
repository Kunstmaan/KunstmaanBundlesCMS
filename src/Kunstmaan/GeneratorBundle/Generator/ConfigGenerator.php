<?php

namespace Kunstmaan\GeneratorBundle\Generator;

/**
 * Generates all config files
 */
class ConfigGenerator extends KunstmaanGenerator
{
    /**
     * Generate all config files.
     */
    public function generate(string $projectDir, bool $overwriteSecurity, bool $overwriteLiipImagine, bool $overwriteFosHttpCache, bool $overwriteFosUser)
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
        $this->renderSingleFile(
            $this->skeletonDir,
            $projectDir . '/config/packages/',
            'fos_user.yaml',
            [],
            true,
            $overwriteFosUser ? 'fos_user.yaml' : 'fos_user.yaml.example'
        );
    }
}
