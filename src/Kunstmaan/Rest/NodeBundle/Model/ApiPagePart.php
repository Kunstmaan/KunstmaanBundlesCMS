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

use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ApiPagePart
 */
class ApiPagePart
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var PagePartInterface
     * @Assert\Valid()
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
     * @return PagePartInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param PagePartInterface $data
     *
     * @return $this
     */
    public function setData(PagePartInterface $data)
    {
        $this->data = $data;
        $this->type = ClassLookup::getClass($data);

        return $this;
    }
}
