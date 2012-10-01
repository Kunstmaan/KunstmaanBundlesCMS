<?php


namespace Kunstmaan\MediaBundle\Helper\Media;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Entity\Folder;

use Symfony\Component\Form\AbstractType;

use Doctrine\ORM\EntityManager;

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
     * @param Media $media
     */
    public abstract function canHandle(Media $media);

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

}