<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\RemoteVideo\RemoteVideoType;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

/**
 * RemoteVideoStrategy
 */
class RemoteVideoHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const CONTENT_TYPE = 'remote/video';
    /**
     * @var string
     */
    const TYPE = 'video';
    /**
     * @var array
     */
    protected $configuration = array();

    /**
     * Constructor. Takes the configuration of the RemoveVideoHandler
     * @param array $configuration
     */
    public function __construct($configuration = array())
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Remote Video Handler';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return RemoteVideoHandler::TYPE;
    }

    /**
     * @return RemoteVideoType
     */
    public function getFormType()
    {
        return new RemoteVideoType($this->configuration);
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
            ($object instanceof Media && $object->getContentType() == RemoteVideoHandler::CONTENT_TYPE)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return RemoteVideoHelper
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
        if (null === $media->getUuid()) {
            $uuid = uniqid();
            $media->setUuid($uuid);
        }
        $video = new RemoteVideoHelper($media);
        $code = $video->getCode();
        //update thumbnail
        switch ($video->getType()) {
            case 'youtube':
                $video->setThumbnailUrl('http://img.youtube.com/vi/' . $code . '/0.jpg');
                break;
            case 'vimeo':
                try {
                    $xml = simplexml_load_file('http://vimeo.com/api/v2/video/' . $code . '.xml');
                    $video->setThumbnailUrl((string)$xml->video->thumbnail_large);
                } catch (\Exception $e) {

                }
                break;
            case 'dailymotion':
                try {
                    $json = json_decode(
                        file_get_contents('https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_large_url')
                    );
                    $thumbnailUrl = $json->{'thumbnail_large_url'};
                    /* dirty hack to fix urls for imagine */
                    if (!$this->endsWith($thumbnailUrl, '.jpg') && !$this->endsWith($thumbnailUrl, '.png')) {
                        $thumbnailUrl = $thumbnailUrl . '&ext=.jpg';
                    }
                    $video->setThumbnailUrl($thumbnailUrl);
                } catch (\Exception $e) {

                }
                break;
        }
    }

    /**
     * String helper
     *
     * @param string $str string
     * @param string $sub substring
     *
     * @return boolean
     */
    private function endsWith($str, $sub)
    {
        return substr($str, strlen($str) - strlen($sub)) === $sub;
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
            'video' => array(
                'path' => 'KunstmaanMediaBundle_folder_videocreate',
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
                case 'www.youtube.com':
                case 'youtube.com':
                    parse_str($parsedUrl['query'], $queryFields);
                    $code = $queryFields['v'];
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('youtube');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Youtube ' . $code);
                    break;
                case 'www.vimeo.com':
                case 'vimeo.com':
                    $code = substr($parsedUrl['path'], 1);
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('vimeo');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Vimeo ' . $code);
                    break;
                case 'www.dailymotion.com':
                case 'dailymotion.com':
                    $code = substr($parsedUrl['path'], 7);
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('dailymotion');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Dailymotion ' . $code);
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
        return 'KunstmaanMediaBundle:Media\RemoteVideo:show.html.twig';
    }

    /**
     * @param Media $media The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $helper = new RemoteVideoHelper($media);

        return $helper->getThumbnailUrl();
    }

    /**
     * @return array
     */
    public function getAddFolderActions()
    {
        return array(
            RemoteVideoHandler::TYPE => array(
                'type' => RemoteVideoHandler::TYPE,
                'name' => 'media.video.add'
            )
        );
    }
}
