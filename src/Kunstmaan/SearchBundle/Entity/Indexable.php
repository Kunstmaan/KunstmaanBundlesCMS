<?php

namespace Kunstmaan\SearchBundle\Entity;

interface Indexable
{

    function getContentForIndexing($container, $entity);
}
