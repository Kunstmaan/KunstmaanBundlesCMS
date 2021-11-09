<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ConfigBundle\Entity\AbstractConfig;
use Kunstmaan\CookieBundle\Form\CookieConfigType;

/**
 * CookieConfig
 *
 * @ORM\Table(name="kuma_cookie_configs")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieConfigRepository")
 */
class CookieConfig extends AbstractConfig
{
    const VISITOR_TYPE_INTERNAL = 'internal traffic';

    const VISITOR_TYPE_CLIENT = 'client traffic';

    const VISITOR_TYPE_NORMAL = 'normal traffic';

    /**
     * @var string
     *
     * @ORM\Column(name="client_ip_addresses", type="string", length=255, nullable=true)
     */
    private $clientIpAddresses;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_ip_addresses", type="string", length=255, nullable=true)
     */
    private $internalIpAddresses;

    /**
     * @var bool
     *
     * @ORM\Column(name="cookie_bundle_enabled", type="boolean", nullable=true)
     */
    private $cookieBundleEnabled = false;

    /**
     * @var int
     *
     * @ORM\Column(name="cookie_version", type="integer", nullable=false)
     */
    private $cookieVersion = 1;

    /**
     * Set clientIpAddresses
     *
     * @param string $clientIpAddresses
     *
     * @return CookieConfig
     */
    public function setclientIpAddresses($clientIpAddresses)
    {
        $this->clientIpAddresses = $clientIpAddresses;

        return $this;
    }

    /**
     * Get clientIpAddresses
     *
     * @return string
     */
    public function getclientIpAddresses()
    {
        return $this->clientIpAddresses;
    }

    /**
     * Set internalIpAddresses
     *
     * @param string $internalIpAddresses
     *
     * @return CookieConfig
     */
    public function setinternalIpAddresses($internalIpAddresses)
    {
        $this->internalIpAddresses = $internalIpAddresses;

        return $this;
    }

    /**
     * Get internalIpAddresses
     *
     * @return string
     */
    public function getinternalIpAddresses()
    {
        return $this->internalIpAddresses;
    }

    /**
     * @return bool
     */
    public function isCookieBundleEnabled()
    {
        return $this->cookieBundleEnabled;
    }

    /**
     * @param bool $cookieBundleEnabled
     */
    public function setCookieBundleEnabled($cookieBundleEnabled)
    {
        $this->cookieBundleEnabled = $cookieBundleEnabled;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCookieVersion()
    {
        return $this->cookieVersion;
    }

    /**
     * @param int $cookieVersion
     */
    public function setCookieVersion($cookieVersion)
    {
        $this->cookieVersion = $cookieVersion;
    }

    /**
     * Returns the form type to use for this configuratble entity.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return CookieConfigType::class;
    }

    /**
     * The internal name will be used as unique id for the route etc.
     *
     * Use a name with no spaces but with underscores.
     *
     * @return string
     */
    public function getInternalName()
    {
        return 'cookieconfig';
    }

    /**
     * Returns the label for the menu item that will be created.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Cookie configuration';
    }
}
