<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\Twig;

use Twig\TwigFunction;

/**
 * @internal
 */
final class UndefinedSymfonyEncoreFunctionHandler
{
    /**
     * @return TwigFunction|false
     */
    public static function handle(string $name)
    {
        if ($name !== 'encore_entry_exists') {
            return false;
        }

        return new TwigFunction($name, static function () { return false; });
    }
}
