<?php


namespace Kunstmaan\MediaBundle\Helper\Media;

use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Form\AbstractType;

/**
 * AbstractMediaHandler
 */
abstract class AbstractMediaHandler
{
    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return AbstractType
     */
    abstract public function getFormType();

    /**
     * @param mixed $media
     */
    abstract public function canHandle($media);

    /**
     * @param Media $media
     *
     * @return mixed
     */
    abstract public function getFormHelper(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    abstract public function prepareMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    abstract public function saveMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    abstract public function updateMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    abstract public function removeMedia(Media $media);

    /**
     * @param mixed $data
     *
     * @return Media
     */
    abstract public function createNew($data);

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media:show.html.twig';
    }

    /**
     * @param Media $media The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        return null;
    }

    /**
     * @return array
     */
    abstract public function getAddFolderActions();
}
