<?php

namespace Kunstmaan\AdminListBundle\AdminList;

interface ListActionInterface
{
    public function getUrl();

    public function getLabel();

    public function getIcon();

    public function getTemplate();

}
