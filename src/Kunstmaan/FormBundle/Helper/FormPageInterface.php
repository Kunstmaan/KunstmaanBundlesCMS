<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * FormPageInterface
 */
interface FormPageInterface
{
    /**
     * Get the email address of the sender of the administrative email
     *
     * @return string
     */
    public function getFromEmail();

    /**
     * Get the email address of the recipient from the administrative email
     *
     * @return string
     */
    public function getToEmail();

    /**
     * Get the subject of the administrative email
     *
     * @return string
     */
    public function getSubject();

    /**
     * Generate the url of the thank you page
     *
     * @param RouterInterface $router  The router
     * @param RenderContext   $context The render context
     *
     * @return string
     */
    public function generateThankYouUrl(RouterInterface $router, RenderContext $context);

    /**
     * Get the page part context used for the form
     *
     * @return string
     */
    public function getFormElementsContext();

    /**
     * Returns the page part configurations which specify which page parts can be added to this form
     *
     * @return array
     */
    public function getPagePartAdminConfigurations();

    /**
     * Returns the default view of this form
     *
     * @return string
     */
    public function getDefaultView();
}
