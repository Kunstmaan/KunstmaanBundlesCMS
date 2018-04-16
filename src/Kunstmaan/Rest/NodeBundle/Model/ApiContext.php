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

/**
 * Class ApiContext
 */
class ApiContext
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $pageParts;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getPageParts()
    {
        return $this->pageParts;
    }

    /**
     * @param array $pageParts
     */
    public function setPageParts($pageParts)
    {
        $this->pageParts = $pageParts;
    }
}
