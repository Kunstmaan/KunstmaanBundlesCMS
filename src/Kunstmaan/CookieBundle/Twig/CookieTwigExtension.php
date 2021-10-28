<?php

namespace Kunstmaan\CookieBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\CookieBundle\Entity\Cookie;
use Kunstmaan\CookieBundle\Entity\CookieConfig;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CookieTwigExtension extends AbstractExtension
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var LegalCookieHelper */
    private $cookieHelper;

    /** @var DomainConfigurationInterface */
    private $domainConfiguration;

    public function __construct(EntityManagerInterface $em, LegalCookieHelper $cookieHelper, DomainConfigurationInterface $domainConfiguration)
    {
        $this->em = $em;
        $this->cookieHelper = $cookieHelper;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_cookie_types', [$this, 'getCookieTypes']),
            new TwigFunction('get_legal_cookie', [$this, 'getLegalCookie']),
            new TwigFunction('get_visitor_type', [$this, 'getVisitorType']),
            new TwigFunction('cookie_in_domain', [$this, 'cookieAllowedForDomain']),
            new TwigFunction('legal_cookie_is_enabled', [$this, 'isLegalCookieEnabled']),
            new TwigFunction('is_granted_for_cookie_bundle', [$this, 'isGrantedForCookieBundle']),
        ];
    }

    public function cookieAllowedForDomain(Cookie $cookie)
    {
        if (empty($cookie->getDomain()) || $this->domainConfiguration->isMultiDomainHost() === false) {
            return true;
        }

        $host = $this->domainConfiguration->getHost();
        $config = $this->domainConfiguration->getFullHostConfig();
        $currentDomain = $config[$host]['id'];

        return $cookie->getDomain() === $currentDomain;
    }

    /**
     * @return array|CookieType[]
     */
    public function getCookieTypes()
    {
        return $this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll();
    }

    /**
     * @return array|mixed
     */
    public function getLegalCookie(Request $request)
    {
        $legalCookie = $this->cookieHelper->getLegalCookie($request);

        return null !== $legalCookie && isset($legalCookie['cookies']) ? $legalCookie['cookies'] : [];
    }

    /**
     * @return string
     */
    public function getVisitorType(Request $request)
    {
        $cookieConfig = $this->em->getRepository('KunstmaanCookieBundle:CookieConfig')->findLatestConfig();

        if (null === $cookieConfig) {
            return CookieConfig::VISITOR_TYPE_NORMAL;
        }

        $clientIpAddresses = array_map('trim', explode(',', $cookieConfig->getclientIpAddresses()));
        $internalIpAddresses = array_map('trim', explode(',', $cookieConfig->getinternalIpAddresses()));

        foreach ($request->getClientIps() as $clientIp) {
            foreach ($clientIpAddresses as $clientIpAddress) {
                if (fnmatch($clientIpAddress, $clientIp)) {
                    return CookieConfig::VISITOR_TYPE_CLIENT;
                }
            }
            foreach ($internalIpAddresses as $internalIpAddress) {
                if (fnmatch($internalIpAddress, $clientIp)) {
                    return CookieConfig::VISITOR_TYPE_INTERNAL;
                }
            }
        }

        return CookieConfig::VISITOR_TYPE_NORMAL;
    }

    /**
     * @param string $internalName
     *
     * @return bool
     */
    public function isLegalCookieEnabled(Request $request, $internalName)
    {
        $cookie = $this->getLegalCookie($request);

        return isset($cookie[$internalName]) && true === $cookie[$internalName];
    }

    /**
     * @return bool
     */
    public function isGrantedForCookieBundle(Request $request)
    {
        return $this->cookieHelper->isGrantedForCookieBundle($request);
    }
}
