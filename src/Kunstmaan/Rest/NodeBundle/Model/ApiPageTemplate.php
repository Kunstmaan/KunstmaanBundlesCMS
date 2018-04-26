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

use Symfony\Component\Validator\Constraints as Assert;

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
     * @var array
     * @Assert\Valid()
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
     * @return array
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param array $contexts
     */
    public function setContexts($contexts)
    {
        $this->contexts = $contexts;
    }
}
