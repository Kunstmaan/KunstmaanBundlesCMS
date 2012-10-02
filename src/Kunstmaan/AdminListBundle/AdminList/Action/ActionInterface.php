<?php

namespace Kunstmaan\AdminListBundle\AdminList\Action;

interface ActionInterface
{

    /**
     * @param $item
     *
     * @return array
     */
    public function getUrlFor($item);

    /**
     * @param $item
     *
     * @return string
     */
    public function getLabelFor($item);

    /**
     * @param $item
     *
     * @return string
     */
    public function getIconFor($item);

    /**
     * @return string
     */
    public function getTemplate();

}
