<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 */
final class SymfonyVersionChecker
{
    public static function isSymfony4()
    {
        return Kernel::VERSION_ID >= 40000;
    }
}
