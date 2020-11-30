<?php

namespace Kunstmaan\NodeBundle\Controller;

use Kunstmaan\NodeBundle\Helper\URLHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UrlReplaceController
{
    /**
     * @var URLHelper
     */
    private $urlHelper;

    public function __construct(URLHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Render a url with the twig url replace filter
     *
     * @Route("/replace", name="KunstmaanNodeBundle_urlchooser_replace", condition="request.isXmlHttpRequest()")
     */
    public function replaceURLAction(Request $request)
    {
        $response = new JsonResponse();

        $response->setData(['text' => $this->urlHelper->replaceUrl($request->get('text'))]);

        return $response;
    }
}
