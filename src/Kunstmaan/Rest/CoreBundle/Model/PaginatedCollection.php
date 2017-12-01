<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\CoreBundle\Model;

use Swagger\Annotations as SWG;

/**
 * Class PaginatedCollection
 *
 * @SWG\Definition()
 */
class PaginatedCollection
{
    /**
     * @var array
     *
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(
     *
     *    )
     * )
     */
    private $items;

    /**
     * @var int
     *
     * @SWG\Property(type="integer")
     */
    private $total;

    /**
     * @var int
     *
     * @SWG\Property(type="integer")
     */
    private $count;

    /**
     * PaginatedCollection constructor.
     * @param array $items
     * @param $totalItems
     */
    public function __construct(array $items, $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = count($items);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return PaginatedCollection
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        $this->count = count($items);

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return PaginatedCollection
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
