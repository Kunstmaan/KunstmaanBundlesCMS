<?php

namespace Kunstmaan\SeoBundle\Helper;


/**
 * Simple helper class to do E-Commerce Tracking.
 * Create one of these objects and pass it along to the google_analytics_ecommerce_tracking Twig function.
 * It'll create the correct calls to the GA script for you.
 *
 * API: https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce?hl=nl
 *
 * Class Order
 *
 * @package Kunstmaan\SeoBundle\Helper
 */
class Order
{

    /**
     * @var string REQUIRED! The unique identifier for this Order/Transaction.
     */
    protected $transactionID;

    /**
     * @param $id number The ID.
     *
     * @return $this
     */
    public function setTransactionID($id)
    {
        $this->transactionID = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @var string The name of the store that handled the Order/Transaction.
     */
    protected $storeName = '';

    /**
     * @param $name string The name of the store.
     *
     * @return $this
     */
    public function setStoreName($name)
    {
        $this->storeName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * REQUIREd!
     * @return string The total. Calculated automatically and returned as string.
     */
    public function getTotal()
    {
        return $this->accumulatePropertyOnOrderItems('getValue');
    }

    /**
     *
     * @return int|string
     */
    public function getTaxesTotal()
    {
        return $this->accumulatePropertyOnOrderItems('getTaxes');
    }

    protected $shippingTotal = '';

    /**
     * @param $total string|number
     *
     * @return $this
     */
    public function setShippingTotal($total)
    {
        $this->shippingTotal = (Double)$total;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTotal()
    {
        return $this->shippingTotal;
    }

    /**
     * @var array(of OrderItem) An array of OrderItem objects.
     */
    public $orderItems = array();

    /**
     * @var string City the order was shipped to.
     */
    protected $city = '';

    /**
     * @param $city string
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @var string State or province the order was shipped to.
     */
    protected $stateOrProvince = '';

    /**
     * @param $stateOrProvince string
     *
     * @return $this
     */
    public function setStateOrProvince($stateOrProvince)
    {
        $this->stateOrProvince = $stateOrProvince;

        return $this;
    }

    /**
     * @return string
     */
   public function getStateOrProvince()
   {
        return $this->stateOrProvince;
   }

    /**
     * @var string Country the order was shipped to.
     */
    protected $country = '';

    /**
     * @param $country string
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }


    /**
     * Loops over the OrderItems and accumulates the value of the given property. Can also be a getter.
     *
     * @param $property
     * @return int|string
     */
    private function accumulatePropertyOnOrderItems($property)
    {
        if (count($this->orderItems) == 0) {
            return '';
        }

        $total = 0;
        foreach ($this->orderItems as $orderItem) {
            $total += $orderItem->$property();
        }

        return $total;
    }
}
