<?php

namespace Kunstmaan\MediaBundle\Helper\Remote;

interface RemoteInterface
{
    public function getName();
    public function setName($name);
    public function getCopyright();
    public function setCopyright($copyright);
    public function getDescription();
    public function setDescription($description);
    public function getCode();
    public function setCode($code);
    public function getThumbnailUrl();
    public function setThumbnailUrl($url);
    public function getType();
    public function setType($type);
} 