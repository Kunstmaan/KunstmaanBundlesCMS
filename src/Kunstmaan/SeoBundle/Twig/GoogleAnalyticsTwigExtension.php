<?php

namespace Kunstmaan\SeoBundle\Twig;

use Kunstmaan\SeoBundle\Helper\Order,
    Kunstmaan\SeoBundle\Helper\OrderConverter,
    Kunstmaan\SeoBundle\Helper\OrderPreparer;

use Twig_Extension,
    Twig_Environment;

/**
 * Twig extensions for Google Analytics
 */
class GoogleAnalyticsTwigExtension extends Twig_Extension
{
    protected $accountVarName = 'account_id';

    protected $accountId;

    /** @var OrderPreparer */
    protected $orderPreparer;

    /** @var OrderConverter */
    protected $orderConverter;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'google_analytics_initialize',
                array($this, 'renderInitialize'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
            new \Twig_SimpleFunction(
                'google_analytics_track_order',
                array($this, 'renderECommerceTracking'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
        );
    }


    /**
     * @param string $id The Google Analytics Account ID.
     */
    public function setAccountID($id)
    {
        $this->accountId = $id;
    }

    /**
     * @param OrderPreparer $preparer
     */
    public function setOrderPreparer($preparer)
    {
        $this->orderConverter = $preparer;
    }

    /**
     * @param OrderConverter $converter
     */
    public function setOrderConverter($converter)
    {
        $this->orderConverter = $converter;
    }

    /**
     * Renders the default Google Analytics JavaScript.
     *
     * If the options are not set it'll try and load the account ID from your parameters (google.analytics.account_id)
     *
     * @param $environment \Twig_Environment
     * @param $options     array|null        Example: {account_id: 'UA-XXXXX-Y'}
     *
     * @return string The HTML rendered.
     *
     * @throws \Twig_Error_Runtime When the Google Analytics ID is nowhere to be found.
     *
     */
    public function renderInitialize(\Twig_Environment $environment, $options = null)
    {
        if (is_null($options)) {
            $options = array();
        }

        $defaults = array();

        $this->setOptionIfNotSet($defaults, $this->accountVarName, $this->accountId);

        // Things set in $options will override things set in $defaults.
        $options = array_merge($defaults, $options);

        if (!$this->isOptionSet($options, $this->accountVarName)) {
            throw new \Twig_Error_Runtime(
                "The google_analytics_initialize function depends on a Google Analytics account ID. You can either pass this along in the initialize_google_analytics function ($this->accountVarName), provide a variable under 'parameters.google.analytics.account_id'."
            );
        }

        $template = $environment->loadTemplate('KunstmaanSeoBundle:GoogleAnalyticsTwigExtension:init.html.twig');

        return $template->render($options);
    }


    /**
     * @param Twig_Environment $environment
     * @param Order            $order
     *
     * @return string The HTML rendered.
     */
    public function renderECommerceTracking(\Twig_Environment $environment, Order $order)
    {
        $order    = $this->orderPreparer->prepare($order);
        $options  = $this->orderConverter->convert($order);
        $template = $environment->loadTemplate(
            'KunstmaanSeoBundle:GoogleAnalyticsTwigExtension:ecommerce_tracking.html.twig'
        );

        return $template->render($options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_google_analytics_twig_extension';
    }


    /**
     * Prefer the given option if already set. Otherwise set the value given.
     *
     * @param array  &$arr   This is modified in place.
     * @param string $option The key in the $arr array.
     * @param mixed  $value  The new value if the option wasn't set already.
     */
    private function setOptionIfNotSet(&$arr, $option, $value)
    {
        if ($this->isOptionSet($arr, $option)) {
            $arr[$option] = $value;
        }
    }

    /**
     * Check if an option is set.
     *
     * @param array  $arr    The array to check.
     * @param string $option The key in the $arr array.
     *
     * @return bool
     */
    private function isOptionSet($arr, $option)
    {
        return (!isset($arr[$option]) || !empty($arr[$option]));
    }
}
