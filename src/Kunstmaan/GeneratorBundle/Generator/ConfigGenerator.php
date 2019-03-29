<?php

namespace Kunstmaan\GeneratorBundle\Generator;

/**
 * Generates all config files
 */
class ConfigGenerator extends KunstmaanGenerator
{
    /**
     * Generate all config files.
     * @param string $projectDir
     * @param bool $overwriteSecurity
     * @param bool $overwriteLiipImagine
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
            $overwriteLiipImagine ? 'liip_imagine.yaml' : 'liip_imagine.yaml.example'
        );
    }
}
