<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;

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
 * RemoteVideoStrategy
 */
class RemoteVideoHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const CONTENT_TYPE = "remote/video";

    /**
     * @return string
     */
    public function getName()
    {
        return "Remote Video Handler";
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
        return new RemoteVideoType();
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if ($media->getContentType() == RemoteVideoHandler::CONTENT_TYPE) {
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
        return new RemoteVideoHelper($media);
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
        return 'KunstmaanMediaBundle:Media\RemoteVideo:show.html.twig';
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
                'video' => array(
                        'path'   => 'KunstmaanMediaBundle_folder_videocreate',
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
            $parsedUrl = parse_url($data);
            parse_str($parsedUrl['query'], $queryFields);
            $code = $queryFields['v'];
            if ($parsedUrl['host'] == 'www.youtube.com') {
                $result = new Media();
                $video = new RemoteVideoHelper($result);
                $video->setType('youtube');
                $video->setCode($code);
                $result = $video->getMedia();
                $result->setName('Youtube ' . $code);
            }
            //TODO: vimeo and dailymotion
        }

        return $result;
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
        $helper = new RemoteVideoHelper($media);
        $code = $helper->getCode();

        return "http://img.youtube.com/vi/" . $code . "/0.jpg";
    }

}