<?php

namespace Kunstmaan\SeoBundle\Helper;

class OrderConverter
{
    /**
     * Converts an Order object to an Array.
     *
     * @return array
     */
    public function convert(Order $order)
    {
        $orderItems = [];

        foreach ($order->orderItems as $orderItem) {
            /* @var OrderItem $orderItem */
            $orderItems[] = [
                'sku' => $orderItem->getSKU(),
                'quantity' => $this->formatNumber($orderItem->getQuantity()),
                'unit_price' => $this->formatNumber($orderItem->getUnitPrice()),
                'taxes' => $this->formatNumber($orderItem->getTaxes()),
                'category_or_variation' => $orderItem->getCategoryOrVariation(),
                'name' => $orderItem->getName(),
            ];
        }

        return [
            'transaction_id' => $order->getTransactionID(),
            'store_name' => $order->getStoreName(),
            'total' => $this->formatNumber($order->getTotal()),
            'taxes_total' => $this->formatNumber($order->getTaxesTotal()),
            'shipping_total' => $this->formatNumber($order->getShippingTotal()),
            'city' => $order->getCity(),
            'state_or_province' => $order->getStateOrProvince(),
            'country' => $order->getCountry(),
            'order_items' => $orderItems,
        ];
    }

    /**
     * Formats a number to a format google an easily comprehend.
     *
     * @param $number number
     *
     * @return string
     */
    protected function formatNumber($number)
    {
        return number_format($number, 2, '.', '');
    }
}
