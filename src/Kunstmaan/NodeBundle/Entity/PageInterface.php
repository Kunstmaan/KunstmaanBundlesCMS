<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\Helper\RenderContext;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The Page Interface
 */
interface PageInterface extends HasNodeInterface
{

    /**
     * @param ContainerInterface $container The Container
     * @param Request            $request   The Request
     * @param RenderContext      $context   The Render context
     *
     * @deprecated Using the service action is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0. Use the SlugActionInterface instead.
     *
     * @return void|RedirectResponse
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context);
}
