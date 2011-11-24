<?php

namespace Kunstmaan\KMediaBundle\Helper\Provider;

use Kunstmaan\KMediaBundle\Entity\Media;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;
use Kunstmaan\KMediaBundle\Helper\Provider\AbstractVideoProvider;

class SpeakerdeckProvider extends AbstractVideoProvider
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