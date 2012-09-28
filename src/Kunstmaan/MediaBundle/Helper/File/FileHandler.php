<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

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
class FileHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const TYPE = 'file';

    /**
     * @var Filesystem
     */
    public $fileSystem = null;

    /**
     * @var MimeTypeGuesserInterface
     */
    public $mimeTypeGuesser = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->fileSystem = new Filesystem(new \Gaufrette\Adapter\Local("uploads/media/"));
        //we use a specific symfony mimetypeguesser because de default (FileinfoMimeTypeGuesser) is unable to recognize MS documents
        $this->mimeTypeGuesser = new FileBinaryMimeTypeGuesser();
    }
    /**
     * @return string
     */
    public function getName()
    {
        return "File Handler";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return FileHandler::TYPE;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Form\VideoType
     */
    public function getFormType()
    {
        return new FileType();
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if (is_file($media->getContent()) || $media->getLocation() == 'local') {
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
        return new FileHelper($media);
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

        $content = $media->getContent();
        if (empty($content)) {
            return;
        }

        if (!$content instanceof File) {
            if (!is_file($content)) {
                throw new \RuntimeException('Invalid file');
            }

            $file = new File($content);
            $media->setContent($file);
        }

        $metadata = array();

        $media->setFileSize(filesize($media->getContent()));
        $media->setMetadata($metadata);

        $media->setContentType($this->mimeTypeGuesser->guess($media->getContent()->getPathname()));
        $media->setLocation('local');
    }

    /**
     * @param Media $media
     */
    public function saveMedia(Media $media)
    {
        if (!$media->getContent() instanceof File) {
            return;
        }

        $originalFile = $this->getOriginalFile($media);
        $originalFile->setContent(file_get_contents($media->getContent()->getRealPath()));
    }

    /**
     * @param Media $media
     *
     * @return \Gaufrette\File
     */
    public function getOriginalFile(Media $media)
    {
        $relativePath = sprintf('/%s.%s', $media->getUuid(), ExtensionGuesser::getInstance()->guess($media->getContentType()));

        return $this->fileSystem->get($relativePath, true);
    }

    /**
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        foreach ($this->formats as $format => $options) {
            $path = $this->generateRelativePath($media, $format);
            if ($this->getFilesystem()->has($path)) {
                $this->getFilesystem()->delete($path);
            }
        }

        // Original
        $path = $this->getOriginalFilePath($media);
        if ($this->getFilesystem()->has($path)) {
            $this->getFilesystem()->delete($path);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {
        $this->saveMedia($media);
    }

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public function createNew($data)
    {
        if ($data instanceof File) {
            $media = new Media();
            $media->setName($data->getClientOriginalName());
            $media->setContent($data);

            return $media;
        }

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
        return null;
    }

    /**
     * @return multitype:string
     */
    public function getAddFolderActions()
    {
        return array(
                FileHandler::TYPE => array(
                        'type' => FileHandler::TYPE,
                        'name' => 'media.file.add')
        );
    }

}