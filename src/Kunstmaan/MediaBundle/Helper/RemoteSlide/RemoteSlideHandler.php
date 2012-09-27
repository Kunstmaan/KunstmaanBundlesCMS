<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteSlide;

use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\MediaBundle\Helper\StrategyInterface;

use Kunstmaan\MediaBundle\Entity\Folder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\AdminList\VideoListConfigurator;
use Kunstmaan\MediaBundle\Entity\Video;

/**
 * RemoteSlideStrategy
 */
class RemoteSlideHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const CONTENT_TYPE = "remote/slide";

    /**
     * @return string
     */
    public function getName()
    {
        return "Remote Slide Handler";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'video';
    }

    /**
     * @return \Kunstmaan\MediaBundle\Form\VideoType
     */
    public function getFormType()
    {
        return new RemoteSlideType();
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if ($media->getContentType() == RemoteSlideHandler::CONTENT_TYPE) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return Video
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
        if (null == $media->getUuid()) {
            $uuid = uniqid();
            $media->setUuid($uuid);
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
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\RemoteSlide:show.html.twig';
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
                        'path'   => 'KunstmaanMediaBundle_folder_slidecreate',
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
        //TODO

        return null;
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     * @param int    $width    The prefered width of the thumbnail
     * @param int    $height   The prefered height of the thumbnail
     *
     * @return string
     */
    public function getThumbnailUrl(Media $media, $basepath, $width = -1, $height = -1)
    {
        //TODO
        return "";

    }
}