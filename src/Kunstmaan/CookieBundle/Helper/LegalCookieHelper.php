<?php

namespace Kunstmaan\CookieBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\CookieBundle\Entity\CookieLog;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LegalCookieHelper
 */
class LegalCookieHelper
{
    const LEGAL_COOKIE_NAME = 'legal_cookie';
    const FUNCTIONAL_COOKIE_NAME = 'functional_cookie';
    const DEFAULT_COOKIE_LIFETIME = 10 * 365 * 24 * 60 * 60; // 10 years

    /** @var array */
    private $legalCookie;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $adminFirewallName;
    /** @var int */
    private $cookieLifetime;

    /**
     * LegalCookieHelper constructor.
     *
     * @param string $adminFirewallName
     */
    public function __construct(EntityManagerInterface $em, $adminFirewallName, $cookieLifeTime = self::DEFAULT_COOKIE_LIFETIME)
    {
        $this->em = $em;
        $this->adminFirewallName = $adminFirewallName;
        $this->cookieLifetime = $cookieLifeTime;
    }

    /**
     * @return array|mixed
     */
    public function findOrCreateLegalCookie(Request $request)
    {
        if (null === $this->legalCookie) {
            $cookies = [];
            if (!$request->cookies->has(self::LEGAL_COOKIE_NAME)) {
                $types = $this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll();
                foreach ($types as $type) {
                    if ($type->isAlwaysOn()) {
                        $cookies['cookies'][$type->getInternalName()] = 'true';
                    } else {
                        $cookies['cookies'][$type->getInternalName()] = 'undefined';
                    }
                }
            }
            $this->legalCookie = $request->cookies->get(self::LEGAL_COOKIE_NAME, json_encode($cookies));
        }

        return json_decode($this->legalCookie, true);
    }

    /**
     * @return array
     */
    public function getLegalCookie(Request $request)
    {
        if (null === $this->legalCookie) {
            $this->legalCookie = $request->cookies->get(self::LEGAL_COOKIE_NAME);
        }

        return json_decode($this->legalCookie, true);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkCookieVersionInResponse(Response $response, Request $request)
    {
        $cookie = $this->getLegalCookie($request);

        $cookieConfig = $this->em->getRepository('KunstmaanCookieBundle:CookieConfig')->findLatestConfig();

        if (null !== $cookieConfig) {
            // If version is different, expire cookie.
            if (isset($cookie['cookie_version']) && $cookieConfig->getCookieVersion() !== $cookie['cookie_version']) {
                $response->headers->clearCookie(self::LEGAL_COOKIE_NAME);
            }
        }

        return $response;
    }

    /**
     * @return Cookie
     */
    public function saveLegalCookie(Request $request, array $legalCookie)
    {
        // Get cookie version.
        $cookieConfig = $this->em->getRepository('KunstmaanCookieBundle:CookieConfig')->findLatestConfig();

        $log = new CookieLog();
        $log->setIpAddress($request->getClientIp());
        $log->setCreated(new \DateTime('now'));

        $this->em->persist($log);
        $this->em->flush();

        $legalCookie['cookie_log_id'] = $log->getId();

        if (null !== $cookieConfig) {
            $legalCookie['cookie_version'] = $cookieConfig->getCookieVersion();
        } else {
            $legalCookie['cookie_version'] = 1;
        }

        return new Cookie(
            self::LEGAL_COOKIE_NAME,
            json_encode($legalCookie),
            time() + ($this->cookieLifetime),
            '/',
            null,
            $request->isSecure(),
            false
        );
    }

    /**
     * @return bool
     */
    public function isGrantedForCookieBundle(Request $request)
    {
        $authenticated = false;

        $cookieConfig = $this->em->getRepository('KunstmaanCookieBundle:CookieConfig')->findLatestConfig();
        if (null !== $cookieConfig) {
            if ($cookieConfig->isCookieBundleEnabled()) {
                return true;
            }
        }

        if ($request->hasSession()) {
            $session = $request->getSession();

            if ($session->isStarted() && $session->has(sprintf('_security_%s', $this->adminFirewallName))) {
                $token = unserialize($session->get(sprintf('_security_%s', $this->adminFirewallName)));

                $authenticated = method_exists($token, 'isAuthenticated') ? $token->isAuthenticated(false) : (bool) $token->getUser();
            }
        }

        return $authenticated;
    }
}
