<?php

namespace Kunstmaan\LiveReloadBundle\EventListener;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\CurlException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ScriptInjectorListener implements EventSubscriberInterface
{
    /** @var Client */
    protected $httpClient;

    /** @var bool */
    protected $checkServerPresence;

    public function __construct(Client $httpClient, $checkServerPresence = true)
    {
	$this->httpClient          = $httpClient;
	$this->checkServerPresence = $checkServerPresence;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request  = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (!$response->headers->has('X-Debug-Token')
            || $response->isRedirection()
            || ($response->headers->has('Content-Type') &&
                false === strpos($response->headers->get('Content-Type'), 'html'))
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
        $pos     = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $script = "livereload.js";

	    if ($this->checkServerPresence) {
                // GET is required, as livereload apparently does not support HEAD requests ...
                $request = $this->httpClient->get($script);
                try {
                    $checkResponse = $this->httpClient->send($request);

                    if ($checkResponse->getStatusCode() !== 200) {
                        return;
                    }
                } catch (CurlException $e) {
                    // If error is connection failed, we assume the server is not running
                    if ($e->getCurlHandle()->getErrorNo() === 7) {
                        return;
                    }
                    throw $e;
                }
            }

            $content = $substrFunction($content, 0, $pos) . "\n"
                . '<script src="' . $this->httpClient->getBaseUrl() . $script . '"></script>' . "\n"
                . $substrFunction($content, $pos);

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
