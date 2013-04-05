<?php

namespace Kunstmaan\SearchBundle\Helper;

interface IndexControllerInterface {

    /**
     * @return boolean
     */
    public function shouldBeIndexed();

}