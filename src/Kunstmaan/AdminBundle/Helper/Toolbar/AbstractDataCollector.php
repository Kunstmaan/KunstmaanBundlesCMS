<?php

namespace Kunstmaan\AdminBundle\Helper\Toolbar;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;

abstract class AbstractDataCollector extends BaseDataCollector implements DataCollectionInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    private $template;

    /**
     * @var AdminRouteHelper
     */
    protected $adminRouteHelper;

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function setAdminRouteHelper(AdminRouteHelper $adminRouteHelper)
    {
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @return bool
     */
    public function showDataCollection(Request $request, Response $response)
    {
        $url = $request->getRequestUri();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest() || $this->adminRouteHelper->isAdminRoute($url) || !$this->isEnabled()) {
            return false;
        }

        if ($response->isRedirection() || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
            || false !== stripos($response->headers->get('Content-Disposition'), 'attachment;')
        ) {
            return false;
        }

        return true;
    }
}
