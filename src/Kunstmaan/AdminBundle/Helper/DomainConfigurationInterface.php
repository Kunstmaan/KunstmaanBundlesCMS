<?php

namespace Kunstmaan\AdminBundle\Helper;

interface DomainConfigurationInterface
{
    /**
     * Return the current host (or the current host override if using the MultiDomainBundle)
     *
     * @return string
     */
    public function getHost();

    /**
     * Return all known hosts.
     *
     * @return array
     */
    public function getHosts();

    /**
     * Get the default locale for the current host.
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Return if the current host is multi language.
     *
     * @return bool
     */
    public function isMultiLanguage();

    /**
     * Return the frontend locales for the current host.
     *
     * @return array
     */
    public function getFrontendLocales();

    /**
     * Return the backend locales for the current host.
     *
     * @return array
     */
    public function getBackendLocales();

    /**
     * Return the root node for the current host (should always be null when the
     * root node does not exist OR you don't use the MultiDomainBundle).
     *
     * @return Kunstmaan\NodeBundle\Entity\Node|null
     */
    public function getRootNode();

    /**
     * Return true if we found multi domain configuration for the current host.
     *
     * @return bool
     */
    public function isMultiDomainHost();

    /**
     * Return optional extra data for the current host from the multi domain
     * configuration. Returns an empty array if no data was defined...
     *
     * @return mixed
     */
    public function getExtraData();

    /**
     * Return optional extra locales data for the current host from the multi domain
     * configuration. Returns an empty array if no data was defined...
     *
     * @return mixed
     */
    public function getLocalesExtraData();
}
