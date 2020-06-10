<?php

namespace Kunstmaan\NodeBundle\Entity;

interface DuplicateSubPageInterface
{
    public function skipClone(): bool;
}
