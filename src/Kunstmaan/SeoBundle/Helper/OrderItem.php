<?php

namespace Kunstmaan\SeoBundle\Helper;


class OrderItem {

    /**
     * @var string REQUIRED! The unique productcode.
     */
    protected $sku;
    public function setSKU($sku) {
        $this->sku = $sku;
        return $this;
    }
    public function getSKU() {
        return $this->sku;
    }

    /**
     * @var string The name of the product.
     */
    protected $name = '';
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function getName() {
        return $this->name;
    }

    /**
     * @var string Category or variation.
     */
    protected $categoryOrVariation = '';
    public function setCategoryOrVariation($catOrVar) {
        $this->categoryOrVariation = $catOrVar;
        return $this;
    }
    public function getCategoryOrVariation() {
        return $this->categoryOrVariation;
    }

    /**
     * @var number REQUIRED! The price of a single unity.
     */
    protected $unitPrice;
    public function setUnitPrice($unitPrice) {
        $this->unitPrice = (Double)$unitPrice;
        return $this;
    }
    public function getUnitPrice() {
        return $this->unitPrice;
    }

    /**
     * @var number REQUIRED! The quantity in which the item was ordered.
     */
    protected $quantity = 1;
    public function setQuantity($quantity) {
        $this->quantity = (Double)$quantity;
        return $this;
    }
    public function getQuantity() {
        return $this->quantity;
    }

    public function getValue() {
        return ($this->unitPrice * $this->quantity);
    }

    /**
     * @var number The amount of taxes. Not a percentage value but the actual value. In total. Not for a single item.
     */
    protected $taxes;
    public function setTaxes($taxes) {
        $this->taxes = (Double)$taxes;
        return $this;
    }
    public function getTaxes() {
        return $this->taxes;
    }
}
