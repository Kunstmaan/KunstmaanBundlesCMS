<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\Twig\Configurator;

use Kunstmaan\AdminBundle\Twig\UndefinedSymfonyEncoreFunctionHandler;
use Symfony\Bundle\TwigBundle\DependencyInjection\Configurator\EnvironmentConfigurator as SymfonyEnvironmentConfigurator;
use Twig\Environment;

/**
 * @internal
 */
final class EnvironmentConfigurator extends SymfonyEnvironmentConfigurator
{
    public function configure(Environment $environment): void
    {
        // Register our undefined function handler before symfony fallback handler
        $environment->registerUndefinedFunctionCallback(function ($name) { return UndefinedSymfonyEncoreFunctionHandler::handle($name); });

        parent::configure($environment);
    }
}
