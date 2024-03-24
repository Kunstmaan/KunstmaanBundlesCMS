<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\Twig;

use Twig\TwigFunction;

/**
 * NEXT_MAJOR Remove compiler pass when groundcontrol setup is removed and webpack encore is the default
 *
 * @internal
 */
final class UndefinedSymfonyEncoreFunctionHandler
{
    public static function handle(string $name): TwigFunction|false
    {
        if ($name !== 'encore_entry_exists') {
            return false;
        }

        return new TwigFunction($name, static function () { return false; });
    }
}
