<?php

namespace Kunstmaan\SeoBundle\Helper;

/**
 * Class OrderItem
 */
class OrderItem
{
    /**
     * @var string REQUIRED! The unique productcode
     */
    protected $sku;

    /**
     * @param $sku string
     *
     * @return $this
     */
    public function setSKU($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return string
     */
    public function getSKU()
    {
        return $this->sku;
    }

    /**
     * @var string the name of the product
     */
    protected $name = '';

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var string category or variation
     */
    protected $categoryOrVariation = '';

    /**
     * @param $catOrVar string
     *
     * @return $this
     */
    public function setCategoryOrVariation($catOrVar)
    {
        $this->categoryOrVariation = $catOrVar;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryOrVariation()
    {
        return $this->categoryOrVariation;
    }

    /**
     * @var number REQUIRED! The price of a single unity
     */
    protected $unitPrice;

    /**
     * REQUIRED!
     *
     * @param $unitPrice number|string
     *
     * @return $this
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = (float) $unitPrice;

        return $this;
    }

    /**
     * @return number
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @var number REQUIRED! The quantity in which the item was ordered
     */
    protected $quantity = 1;

    /**
     * REQUIRED!
     *
     * @param $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (float) $quantity;

        return $this;
    }

    /**
     * @return int|number
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * The total value of the OrderItem (excluding taxes)
     *
     * @return number
     */
    public function getValue()
    {
        return $this->unitPrice * $this->quantity;
    }

    /**
     * @var number The amount of taxes. Not a percentage value but the actual value. In total. Not for a single item.
     */
    protected $taxes;

    /**
     * @param $taxes
     *
     * @return $this
     */
    public function setTaxes($taxes)
    {
        $this->taxes = (float) $taxes;

        return $this;
    }

    /**
     * @return number
     */
    public function getTaxes()
    {
        return $this->taxes;
    }
}
