<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SessionSecurityListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $ipCheck;

    /**
     * @var bool
     */
    private $userAgentCheck;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @param bool $ipCheck
     * @param bool $userAgentCheck
     * @param LoggerInterface $logger
     */
    public function __construct($ipCheck, $userAgentCheck, LoggerInterface $logger)
    {
        $this->ipCheck = $ipCheck;
        $this->userAgentCheck = $userAgentCheck;
        $this->logger = $logger;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // Make sure the ip and user agent is stored in the session
        $request = $event->getRequest();
        if ($request->hasSession() && $request->getSession()->isStarted()) {
            $session = $request->getSession();
            if ($this->ipCheck && !$session->has('kuma_ip')) {
                $session->set('kuma_ip', $this->getIp($request));
            }
            if ($this->userAgentCheck && !$session->has('kuma_ua')) {
                $session->set('kuma_ua', $this->getUserAgent($request));
            }
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->hasSession() && $request->getSession()->isStarted()) {
            $session = $request->getSession();

            // Check that the ip matches
            if ($this->ipCheck && $session->has('kuma_ip') && $session->get('kuma_ip') != $this->getIp($request)) {
                $this->logger->error(sprintf(
                    "Session ip '%s' does not match with request ip '%s', invalidating the current session",
                    $session->get('kuma_ip'),
                    $this->getIp($request)
                ));
                $this->invalidateSession($session, $request);
            }

            // Check that the user agent matches
            if ($this->userAgentCheck && $session->has('kuma_ua') && $session->get('kuma_ua') != $this->getUserAgent($request)) {
                $this->logger->error(sprintf(
                    "Session user agent '%s' does not match with request user agent '%s', invalidating the current session",
                    $session->get('kuma_ua'),
                    $this->getUserAgent($request)
                ));
                $this->invalidateSession($session, $request);
            }
        }
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     */
    private function invalidateSession(SessionInterface $session, Request $request)
    {
        $session->invalidate();
        $session->set('kuma_ip', $this->getIp($request));
        $session->set('kuma_ua', $this->getUserAgent($request));
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getIp(Request $request)
    {
        if (!$this->ip) {
            $forwarded = $request->server->get('HTTP_X_FORWARDED_FOR');
            if (strlen($forwarded) > 0) {
                $parts = explode(',', $forwarded);
                $parts = array_map('trim', $parts);
                $parts = array_filter($parts);
                if (count($parts) > 0) {
                    $ip = $parts[0];
                }
            }
            if (empty($ip)) {
                $ip = $request->getClientIp();
            }
            $this->ip = $ip;
        }

        return $this->ip;
    }

    /**
     * @param Request $request
     * @return array|string
     */
    private function getUserAgent(Request $request)
    {
        if (!$this->userAgent) {
            $this->userAgent = $request->headers->get('User-Agent');
        }

        return $this->userAgent;
    }
}
