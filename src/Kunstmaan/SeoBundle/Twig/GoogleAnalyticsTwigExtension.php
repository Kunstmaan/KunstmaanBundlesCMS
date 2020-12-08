<?php

namespace Kunstmaan\SeoBundle\Twig;

use Kunstmaan\SeoBundle\Helper\Order;
use Kunstmaan\SeoBundle\Helper\OrderConverter;
use Kunstmaan\SeoBundle\Helper\OrderPreparer;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extensions for Google Analytics
 *
 * @final since 5.4
 */
class GoogleAnalyticsTwigExtension extends AbstractExtension
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
        return [
            new TwigFunction(
                'google_analytics_initialize',
                [$this, 'renderInitialize'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'google_analytics_track_order',
                [$this, 'renderECommerceTracking'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param string $id the Google Analytics Account ID
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
     * @param array|null $options Example: {account_id: 'UA-XXXXX-Y'}
     *
     * @return string the HTML rendered
     *
     * @throws Error when the Google Analytics ID is nowhere to be found
     */
    public function renderInitialize(Environment $environment, $options = null)
    {
        if (\is_null($options)) {
            $options = [];
        }

        $defaults = [];

        $this->setOptionIfNotSet($defaults, $this->accountVarName, $this->accountId);

        // Things set in $options will override things set in $defaults.
        $options = array_merge($defaults, $options);

        if (!$this->isOptionSet($options, $this->accountVarName)) {
            throw new RuntimeError("The google_analytics_initialize function depends on a Google Analytics account ID. You can either pass this along in the initialize_google_analytics function ($this->accountVarName), provide a variable under 'parameters.google.analytics.account_id'.");
        }

        $template = $environment->load('@KunstmaanSeo/GoogleAnalyticsTwigExtension/init.html.twig');

        return $template->render($options);
    }

    /**
     * @return string the HTML rendered
     */
    public function renderECommerceTracking(Environment $environment, Order $order)
    {
        $order = $this->orderPreparer->prepare($order);
        $options = $this->orderConverter->convert($order);
        $template = $environment->load(
            '@KunstmaanSeo/GoogleAnalyticsTwigExtension/ecommerce_tracking.html.twig'
        );

        return $template->render($options);
    }

    /**
     * Prefer the given option if already set. Otherwise set the value given.
     *
     * @param array  &$arr   This is modified in place
     * @param string $option the key in the $arr array
     * @param mixed  $value  the new value if the option wasn't set already
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
     * @param array  $arr    the array to check
     * @param string $option the key in the $arr array
     *
     * @return bool
     */
    private function isOptionSet($arr, $option)
    {
        return !isset($arr[$option]) || !empty($arr[$option]);
    }
}
