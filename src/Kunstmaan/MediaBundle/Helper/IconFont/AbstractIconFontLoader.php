<?php

namespace Kunstmaan\MediaBundle\Helper\IconFont;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * AbstractIconFontLoader
 */
abstract class AbstractIconFontLoader implements IconFontLoaderInterface
{
    /**
     * @var string
     */
    protected $rootPath;

    public function __construct(KernelInterface $kernel)
    {
        $this->rootPath = $kernel->getProjectDir();
    }
}
