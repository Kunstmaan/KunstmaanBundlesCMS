<?php

namespace Kunstmaan\MediaBundle\Helper\Provider;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * ProviderInterface
 */
interface ProviderInterface
{
    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name   The name
     * @param array  $format The format
     *
     * @return void
     */
    public function addFormat($name, array $format);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFormat($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getFormat($name);

    /**
     * @return array
     */
    public function getFormats();

    /**
     * @param array $formats
     *
     * @return void
     */
    public function setFormats(array $formats);

    /**
     * @param Media  $media  The media
     * @param string $format The format
     *
     * @return string
     */
    public function getMediaUrl(Media $media, $format = null);

    /**
     * @param \Ano\Bundle\MediaBundle\Model\Media $media
     *
     * @return void
     */
    public function prepareMedia(Media $media);

    /**
     * @param \Ano\Bundle\MediaBundle\Model\Media $media
     *
     * @return void
     */
    public function saveMedia(Media $media);

    /**
     * @param \Ano\Bundle\MediaBundle\Model\Media $media
     *
     * @return void
     */
    public function updateMedia(Media $media);

    /**
     * @param \Ano\Bundle\MediaBundle\Model\Media $media
     *
     * @return void
     */
    public function removeMedia(Media $media);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param Media  $media   The media
     * @param string $format  The format
     * @param array  $options The options
     *
     * @return string
     */
    public function renderRaw(Media $media, $format = null, array $options = array());

    /**
     * @param Media  $media   The media
     * @param string $format  The format
     * @param array  $options The options
     *
     * @return array Merged options
     */
    public function getRenderOptions(Media $media, $format, array $options = array());
}