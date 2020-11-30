<?php

namespace Kunstmaan\SeoBundle\Helper;

/**
 * Prepares an order with it's orderitems for conversion.
 */
class OrderPreparer
{
    /**
     * Fully prepares an order for conversion.
     * What it does for now is deduplicate the OrderItems.
     *
     * Ensure every orderItem has a unique SKU.
     * Only one request is made per order/SKU.
     * So the same SKUs on multiple lines need to be grouped.
     *
     * @return Order
     */
    public function prepare(Order $order)
    {
        /** @var OrderItem[] $orderItems */
        $orderItems = $order->orderItems;

        /** @var OrderItem[] $newOrderItems */
        $newOrderItems = [];

        foreach ($orderItems as $item) {
            if (!isset($newOrderItems[$item->getSKU()])) {
                $newOrderItems[$item->getSKU()] = $item;
            } else {
                $newOrderItems[$item->getSKU()]->setQuantity($newOrderItems[$item->getSKU()]->getQuantity() + $item->getQuantity());
                $newOrderItems[$item->getSKU()]->setTaxes($newOrderItems[$item->getSKU()]->getTaxes() + $item->getTaxes());
            }
        }

        $order->orderItems = $newOrderItems;

        return $order;
    }
}
