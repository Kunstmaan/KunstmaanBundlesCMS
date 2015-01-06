<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteAudio;

use Kunstmaan\MediaBundle\Form\RemoteAudio\RemoteAudioType;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * RemoteAudioStrategy
 */
class RemoteAudioHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    private $soundcloudApiKey;

    /**
     * @var string
     */
    const CONTENT_TYPE = 'remote/audio';

    /**
     * @var string
     */
    const TYPE = 'audio';

    public function __construct($soundcloudApiKey)
    {
        $this->soundcloudApiKey = $soundcloudApiKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Remote Audio Handler';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return RemoteAudioHandler::TYPE;
    }

    /**
     * @return RemoteAudioType
     */
    public function getFormType()
    {
        return new RemoteAudioType();
    }

    /**
     * @return mixed
     */
    public function getSoundcloudApiKey()
    {
        return $this->soundcloudApiKey;
    }

    /**
     * @param mixed $object
     *
     * @return bool
     */
    public function canHandle($object)
    {
        if (
            (is_string($object)) ||
            ($object instanceof Media && $object->getContentType() == RemoteAudioHandler::CONTENT_TYPE)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return RemoteAudioHelper
     */
    public function getFormHelper(Media $media)
    {
        return new RemoteAudioHelper($media);
    }

    /**
     * @param Media $media
     *
     * @throws \RuntimeException when the file does not exist
     */
    public function prepareMedia(Media $media)
    {
        if (null === $media->getUuid()) {
            $uuid = uniqid();
            $media->setUuid($uuid);
        }
        $audio = new RemoteAudioHelper($media);
        $code  = $audio->getCode();
        //update thumbnail
        switch ($audio->getType()) {
            case 'soundcloud':
                $scData     = json_decode(
                    file_get_contents(
                        'http://api.soundcloud.com/tracks/' . $code . '.json?client_id=' . $this->getSoundcloudApiKey()
                    )
                );
                $artworkUrl = $scData->artwork_url;
                $artworkUrl = str_replace('large.jpg', 't500x500.jpg', $artworkUrl);
                $audio->setThumbnailUrl($artworkUrl);
                break;
        }
    }

    /**
     * @param Media $media
     */
    public function saveMedia(Media $media)
    {
    }

    /**
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function createNew($data)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\RemoteAudio:show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $helper = new RemoteAudioHelper($media);

        return $helper->getThumbnailUrl();
    }

    /**
     * @return array
     */
    public function getAddFolderActions()
    {
        return array(
            RemoteAudioHandler::TYPE => array(
                'type' => RemoteAudioHandler::TYPE,
                'name' => 'media.audio.add'
            )
        );
    }
}
