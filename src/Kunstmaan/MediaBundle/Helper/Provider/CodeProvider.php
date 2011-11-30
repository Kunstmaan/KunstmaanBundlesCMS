<?php

namespace Kunstmaan\MediaBundle\Helper\Provider;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser;
use Kunstmaan\MediaBundle\Helper\Provider\AbstractVideoProvider;

class CodeProvider extends AbstractVideoProvider
{
    /* @var string */
    protected $template = '';

    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        $metadata = $this->getMetadata($media);
        $metadata['uuid'] = $media->getContent();

        $media->setMetadata($metadata);
    }

    protected function getMetadata(Media $media)
    {
        if (!$media->getContent()) {
            return null;
        }

        return array("content" => $media->getContent());
    }

    /**
     * {@inheritDoc}
     *
     * Source and copyright : https://github.com/sonata-project/SonataMediaBundle/blob/master/Provider/DailyMotionProvider.php
     */
    public function getRenderOptions(Media $media, $format, array $options = array())
    {
        return $options;
    }
}