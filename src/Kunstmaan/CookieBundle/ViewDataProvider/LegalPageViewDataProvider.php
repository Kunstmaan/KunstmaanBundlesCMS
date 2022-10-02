<?php

declare(strict_types=1);

namespace Kunstmaan\CookieBundle\ViewDataProvider;

use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class LegalPageViewDataProvider implements PageViewDataProviderInterface
{
    /** @var RequestStack */
    private $requestStack;
    /** @var LegalCookieHelper */
    private $cookieHelper;

    public function __construct(RequestStack $requestStack, LegalCookieHelper $cookieHelper)
    {
        $this->requestStack = $requestStack;
        $this->cookieHelper = $cookieHelper;
    }

    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return;
        }

        if (!$this->cookieHelper->isGrantedForCookieBundle($request)) {
            throw new NotFoundHttpException('Not found');
        }
    }
}
