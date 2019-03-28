<?php

namespace Kunstmaan\GeneratorBundle\Generator;

/**
 * Generates all config files
 */
class ConfigGenerator extends KunstmaanGenerator
{
    /**
     * Generate all config files.
     *
     */
    public function generate(string $projectDir, bool $overwriteSecurity, bool $overwriteLiipImagine)
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
            $overwriteSecurity ? 'liip_imagine.yaml' : 'liip_imagine.yaml.example'
        );
    }
}
