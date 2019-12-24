<?php

namespace Kunstmaan\MediaBundle\Entity;

interface ConfigurableMediaInterface
{
    public function getRunTimeConfig();
    public function setRunTimeConfig(string $runTimeConfig);
    public function getMedia();
}
