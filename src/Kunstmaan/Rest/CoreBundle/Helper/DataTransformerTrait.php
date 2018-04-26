<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\CoreBundle\Helper;

use Kunstmaan\Rest\CoreBundle\Service\DataTransformerService;

/**
 * Trait DataTransformerTrait
 */
trait DataTransformerTrait
{
    /** @var DataTransformerService */
    private $dataTransformer;

    /**
     * Create a transformer decorator to pass through other services and methods
     *
     * @return \Closure
     */
    public function createTransformerDecorator()
    {
        $transformer = $this->dataTransformer;
        $decorator = function ($data) use ($transformer) {
            return $transformer->transform($data);
        };

        return $decorator;
    }
}
