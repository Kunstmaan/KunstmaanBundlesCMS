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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ApiPageTemplate
 */
class ApiPageTemplate
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection
     */
    private $contexts;

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
     * @return ArrayCollection
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param ArrayCollection $contexts
     */
    public function setContexts($contexts)
    {
        $this->contexts = $contexts;
    }
}
