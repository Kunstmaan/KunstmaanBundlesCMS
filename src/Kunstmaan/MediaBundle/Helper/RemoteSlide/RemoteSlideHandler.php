<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteSlide;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\RemoteSlide\RemoteSlideType;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

/**
 * RemoteSlideStrategy
 */
class RemoteSlideHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const CONTENT_TYPE = 'remote/slide';

    const TYPE = 'slide';

    /**
     * @return string
     */
    public function getName()
    {
        return 'Remote Slide Handler';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return RemoteSlideHandler::TYPE;
    }

    /**
     * @return RemoteSlideType
     */
    public function getFormType()
    {
        return new RemoteSlideType();
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
            ($object instanceof Media && $object->getContentType() == RemoteSlideHandler::CONTENT_TYPE)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return RemoteSlideHelper
     */
    public function getFormHelper(Media $media)
    {
        return new RemoteSlideHelper($media);
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
        $slide = new RemoteSlideHelper($media);
        $code = $slide->getCode();
        // update thumbnail
        switch ($slide->getType()) {
            case 'slideshare':
                try {
                    $json = json_decode(
                        file_get_contents(
                            'http://www.slideshare.net/api/oembed/2?url=http://www.slideshare.net/slideshow/embed_code/' . $code . '&format=json'
                        )
                    );
                    $slide->setThumbnailUrl($json->thumbnail);
                } catch (\ErrorException $e) {
                    // Silent exception - should not bubble up since failure to create a thumbnail is not a fatal error
                }
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
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
            'slide' => array(
                'path' => 'KunstmaanMediaBundle_folder_slidecreate',
                'params' => array(
                    'folderId' => $params['folderId']
                )
            )
        );
    }

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public function createNew($data)
    {
        $result = null;
        if (is_string($data)) {
            if (strpos($data, 'http') !== 0) {
                $data = 'http://' . $data;
            }
            $parsedUrl = parse_url($data);
            switch ($parsedUrl['host']) {
                case 'www.slideshare.net':
                case 'slideshare.net':
                    $result = new Media();
                    $slide = new RemoteSlideHelper($result);
                    $slide->setType('slideshare');
                    $json = json_decode(
                        file_get_contents('http://www.slideshare.net/api/oembed/2?url=' . $data . '&format=json')
                    );
                    $slide->setCode($json->{"slideshow_id"});
                    $result = $slide->getMedia();
                    $result->setName('SlideShare ' . $data);
                    break;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\RemoteSlide:show.html.twig';
    }

    /**
     * @param Media $media The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $helper = new RemoteSlideHelper($media);

        return $helper->getThumbnailUrl();
    }

    /**
     * @return array
     */
    public function getAddFolderActions()
    {
        return array(
            RemoteSlideHandler::TYPE => array(
                'type' => RemoteSlideHandler::TYPE,
                'name' => 'media.slide.add'
            )
        );
    }
}
