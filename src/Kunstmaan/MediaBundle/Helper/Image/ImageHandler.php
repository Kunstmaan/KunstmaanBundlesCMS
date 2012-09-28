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

    /**
     * @param ImageManipulatorInterface $imageManipulator
     */
    public function __construct(ImageManipulatorInterface $imageManipulator)
    {
      $this->imageManipulator = $imageManipulator;
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
     * @param int    $width    The prefered width of the thumbnail
     * @param int    $height   The prefered height of the thumbnail
     *
     * @return string
     */
    public function getThumbnailUrl(Media $media, $basepath, $width = -1, $height = -1)
    {
        //TODO: use manipulator
        /*$this->imageManipulator->resize(
                $media,
                $originalFile,
                $this->filesystem->get($this->generateRelativePath($media, $format), true),
                $options
        );*/
        $localPath = '/uploads/media/'.$media->getUrl();

        return $basepath . $localPath;
    }

}