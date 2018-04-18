<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Model;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Class ApiEntity
 */
class ApiEntity
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var EntityInterface
     */
    private $data;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param EntityInterface $data
     *
     * @return $this
     */
    public function setData(EntityInterface $data)
    {
        $this->data = $data;
        $this->type = ClassLookup::getClass($data);

        return $this;
    }
}
