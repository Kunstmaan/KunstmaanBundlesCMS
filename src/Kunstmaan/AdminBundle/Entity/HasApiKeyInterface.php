<?php

namespace Kunstmaan\AdminBundle\Entity;

interface HasApiKeyInterface
{
    public function getApiKey();

    public function setApiKey($key);
}
