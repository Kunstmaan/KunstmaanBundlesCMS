<?php

namespace Kunstmaan\MediaBundle\Helper\Image;

use Kunstmaan\MediaBundle\Helper\Manipulator\ImageManipulatorInterface;

use Kunstmaan\MediaBundle\Helper\File\FileHandler;

use Kunstmaan\MediaBundle\Form\File\FileType;

use Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser;

use Gaufrette\Filesystem;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

use Gaufrette\Adapter\Local;

use Symfony\Component\HttpFoundation\File\File;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\MediaBundle\Helper\StrategyInterface;

use Kunstmaan\MediaBundle\Entity\Folder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\AdminList\VideoListConfigurator;
use Kunstmaan\MediaBundle\Entity\Video;

/**
 * FileHandler
 */
class ImageHandler extends FileHandler
{

    /**
     * @var ImageManipulatorInterface
     */
    protected $imageManipulator;

    protected $aviaryApiKey;

    /**
     * @param ImageManipulatorInterface $imageManipulator The image manipulator
     * @param string                    $aviaryApiKey     The aviary key
     */
    public function __construct(ImageManipulatorInterface $imageManipulator, $aviaryApiKey)
    {
        parent::__construct();
        $this->imageManipulator = $imageManipulator;
        $this->aviaryApiKey = $aviaryApiKey;
    }

    /**
     * @return string
     */
    public function getAviaryApiKey()
    {
        return $this->aviaryApiKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "Image Handler";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if (parent::canHandle($media) && strpos($media->getContentType(), 'image') === 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\Image:show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $localPath = '/uploads/media/'.$media->getUrl();

        return $basepath . $localPath;
    }

}