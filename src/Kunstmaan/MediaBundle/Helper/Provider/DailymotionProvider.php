<?php

namespace Kunstmaan\KMediaBundle\Helper\Provider;

use Kunstmaan\KMediaBundle\Entity\Media;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;

class DailymotionProvider extends AbstractVideoProvider
{
    /* @var string */
    protected $template = 'AnoMediaBundle:Dailymotion:view.html.twig';

    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        $metadata = $this->getMetadata($media);
        $metadata['uuid'] = $media->getContent();

        $media->setMetadata($metadata);
        $media->setName($metadata['title']);
        $media->setHeight($metadata['height']);
        $media->setWidth($metadata['width']);
    }

    protected function getMetadata(Media $media)
    {
        if (!$media->getContent()) {
            return null;
        }

        $url = sprintf('http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/%s&format=json', $media->getContent());
        $metadata = @file_get_contents($url);

        if (!$metadata) {
            throw new \RuntimeException('Unable to retrieve dailymotion video information for :' . $url);
        }

        $metadata = json_decode($metadata, true);

        if (!$metadata) {
            throw new \RuntimeException('Unable to decode dailymotion video information for :' . $url);
        }

        return $metadata;
    }

    /**
     * {@inheritDoc}
     *
     * Source and copyright : https://github.com/sonata-project/SonataMediaBundle/blob/master/Provider/DailyMotionProvider.php
     */
    public function getRenderOptions(Media $media, $format, array $options = array())
    {
        // documentation : http://www.dailymotion.com/en/doc/api/player

        $defaults = array(
            // Values: 0 or 1. Default is 0. Determines if the player loads related videos when
            // the current video begins playback.
            'related'   => 0,

            // Values: 0 or 1. Default is 1. Determines if the player allows explicit content to
            // be played. This parameter may be added to embed code by platforms which do not
            // want explicit content to be posted by their users.
            'explicit'  => 0,

            // Values: 0 or 1. Default is 0. Determines if the video will begin playing
            // automatically when the player loads.
            'autoPlay'      => 0,

            // Values: 0 or 1. Default is 0. Determines if the video will begin muted.
            'autoMute' => 0,

            // Values: 0 or 1. Default is 0. Determines if the video will unmuted on mouse over.
            // Of course it works only if the player is on automute=1.
            'unmuteOnMouseOver' => 0,

            // Values: a number of seconds. Default is 0. Determines if the video will begin
            // playing the video at a given time.
            'start' => 0,

            // Values: 0 or 1. Default is 0. Enable the Javascript API by setting this parameter
            // to 1. For more information and instructions on using the Javascript API, see the
            // JavaScript API documentation.
            'enableApi' => 0,

            // Values: 0 or 1. Default is 0. Determines if the player should display controls
            // or not during video playback.
            'chromeless' => 0,

            // Values: 0 or 1. Default is 0. Determines if the video should be expended to fit
            // the whole player's size.
            'expendVideo' => 0,
            'color2' => null,

            // Player color changes may be set using color codes. A color is described by its
            // hexadecimal value (eg: FF0000 for red).
            'foreground' => null,
            'background' => null,
            'highlight' => null,
        );


        $player_parameters =  array_merge($defaults, isset($options['player_parameters']) ? $options['player_parameters'] : array());

        $params = array(
            'player_parameters' => http_build_query($player_parameters),
            'allowFullScreen'   => isset($options['allowFullScreen'])   ? $options['allowFullScreen']     : 'true',
            'allowScriptAccess' => isset($options['allowScriptAccess']) ? $options['allowScriptAccess'] : 'always',
            'width'             => isset($options['width'])             ? $options['width']  : $media->getWidth(),
            'height'            => isset($options['height'])            ? $options['height'] : $media->getHeight(),
        );

        return $params;
    }
}