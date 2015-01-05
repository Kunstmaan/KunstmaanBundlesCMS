<?php

namespace Kunstmaan\LiveReloadBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ScriptInjectorListener implements EventSubscriberInterface {

    protected $enabled;
    protected $host;
    protected $port;
    protected $check_server_presence;

    public function __construct($host = 'localhost', $port = 35729, $enabled = true, $check_server_presence = true)
    {
        $this->host = $host;
        $this->port = $port;
        $this->enabled = $enabled;
        $this->check_server_presence = $check_server_presence;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (!$this->enabled
            || !$response->headers->has('X-Debug-Token')
            || $response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $this->injectScript($response);
    }

    /**
     * Injects the livereload script.
     *
     * @param Response $response A Response instance
     */
    protected function injectScript(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $script = "http://$this->host:$this->port/livereload.js";

            if ($this->check_server_presence) {
                $headers = get_headers($script);
                if (!is_array($headers) || strpos($headers[0], '200') === false) {
                    return;
                }
            }

            $content = $substrFunction($content, 0, $pos)."\n"
                .'<script src="'.$script.'"></script>'."\n"
                .$substrFunction($content, $pos);

            $response->setContent($content);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -127),
        );
    }

}
