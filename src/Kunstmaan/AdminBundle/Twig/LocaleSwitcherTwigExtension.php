<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocaleSwitcherTwigExtension extends AbstractExtension
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * Get Twig functions defined in this extension.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('localeswitcher_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('get_locales', [$this, 'getLocales']),
            new TwigFunction('get_backend_locales', [$this, 'getBackendLocales']),
        ];
    }

    /**
     * Render locale switcher widget.
     *
     * @param array  $locales    The locales
     * @param string $route      The route
     * @param array  $parameters The route parameters
     */
    public function renderWidget(Environment $env, $locales, $route, array $parameters = []): string
    {
        $template = $env->load(
            '@KunstmaanAdmin/LocaleSwitcherTwigExtension/widget.html.twig'
        );

        return $template->render(
            array_merge(
                $parameters,
                [
                    'locales' => $locales,
                    'route' => $route,
                ]
            )
        );
    }

    public function getLocales(): array
    {
        return $this->domainConfiguration->getFrontendLocales();
    }

    public function getBackendLocales($switchedHost = null): array
    {
        return $this->domainConfiguration->getBackendLocales($switchedHost);
    }
}
