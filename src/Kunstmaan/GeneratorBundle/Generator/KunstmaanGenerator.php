<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class that contains all common generator logic.
 */
class KunstmaanGenerator extends Generator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $skeletonDir;

    /**
     * @var CommandAssistant
     */
    protected $assistant;

    /**
     * @param Filesystem        $filesystem  The filesystem
     * @param RegistryInterface $registry    The registry
     * @param string            $skeletonDir The directory of the skeleton
     * @param CommandAssistant  $assistant  The command assistant
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry, $skeletonDir, CommandAssistant $assistant)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->skeletonDir = GeneratorUtils::getFullSkeletonPath($skeletonDir);
        $this->assistant = $assistant;

        $this->setSkeletonDirs(array($this->skeletonDir));
    }

    /**
     * Check that the keyword is a reserved word for the database system.
     *
     * @param string $keyword
     * @return boolean
     */
    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }
}
