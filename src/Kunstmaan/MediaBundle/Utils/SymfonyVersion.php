<?php

namespace Kunstmaan\MediaBundle\Utils;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 */
class SymfonyVersion
{
    /**
     * @return string
     */
    public static function getRootWebPath()
    {
        return sprintf('%%kernel.project_dir%%/%s', self::isKernelLessThan(4) ? 'web' : 'public');
    }

    /**
     * @return bool
     */
    public static function isKernelLessThan($major, $minor = null, $patch = null)
    {
        return static::kernelVersionCompare('<', $major, $minor, $patch);
    }

    /**
     * @return bool
     */
    private static function kernelVersionCompare($operator, $major, $minor = null, $patch = null)
    {
        return version_compare(Kernel::VERSION_ID, sprintf("%d%'.02d%'.02d", $major, $minor ?: 0, $patch ?: 0), $operator);
    }
}
