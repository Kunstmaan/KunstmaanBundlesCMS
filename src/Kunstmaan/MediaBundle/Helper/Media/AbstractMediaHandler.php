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
    public abstract function getName();

    /**
     * @return string
     */
    public abstract function getType();

    /**
     * @return AbstractType
     */
    public abstract function getFormType();

    /**
     * @param mixed $media
     */
    public abstract function canHandle($media);

    /**
     * @param Media $media
     *
     * @return mixed
     */
    public abstract function getFormHelper(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    public abstract function prepareMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    public abstract function saveMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    public abstract function updateMedia(Media $media);

    /**
     * @param Media $media
     *
     * @return void
     */
    public abstract function removeMedia(Media $media);

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public abstract function createNew($data);

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media:show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
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
    public abstract function getAddFolderActions();

}