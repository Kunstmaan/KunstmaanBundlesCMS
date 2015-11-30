<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\File\FileType;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;

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
     * @var string
     */
    const MEDIA_PATH = '/uploads/media/';

    /**
     * @var Filesystem
     */
    public $fileSystem = null;

    /**
     * @var MimeTypeGuesserInterface
     */
    public $mimeTypeGuesser = null;

    /**
     * Files with a blacklisted extension will be converted to txt
     *
     * @var array
     */
    private $blacklistedExtensions = array();

    /**
     * Constructor
     * @param int $priority
     * @param MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory
     */
    public function __construct($priority, MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory)
    {
        parent::__construct($priority);
        $this->mimeTypeGuesser = $mimeTypeGuesserFactory->get();
    }

    /**
     * Inject the blacklisted
     *
     * @param array $blacklistedExtensions
     */
    public function setBlacklistedExtensions(array $blacklistedExtensions)
    {
        $this->blacklistedExtensions = $blacklistedExtensions;
    }

    /**
     * Inject the root dir so we know the full path where we need to store the file.
     *
     * @param string $kernelRootDir
     */
    public function setMediaPath($kernelRootDir)
    {
        $this->fileSystem = new Filesystem(new Local($kernelRootDir . '/../web' . self::MEDIA_PATH, true));
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
     * @return FileType
     */
    public function getFormType()
    {
        return new FileType();
    }

    /**
     * @param mixed $object
     *
     * @return bool
     */
    public function canHandle($object)
    {
        if ($object instanceof File ||
            ($object instanceof Media &&
                (is_file($object->getContent()) || $object->getLocation() == 'local'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return FileHelper
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
        if (null === $media->getUuid()) {
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
        if ($content instanceof UploadedFile) {
            $media->setOriginalFilename($content->getClientOriginalName());
            $name = $media->getName();
            if (empty($name)) {
                $media->setName($media->getOriginalFilename());
            }
        }

        $media->setFileSize(filesize($media->getContent()));

        $contentType = $this->mimeTypeGuesser->guess($media->getContent()->getPathname());
        $media->setContentType($contentType);
        $media->setUrl(self::MEDIA_PATH . $this->getFilePath($media));
        $media->setLocation('local');
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
        $this->saveMedia($media);
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
        return $this->fileSystem->get($this->getFilePath($media), true);
    }

    /**
     *
     *
     * @param Media $media
     * @return string
     */
    private function getFilePath(Media $media)
    {
        $filename  = $media->getOriginalFilename();
        $filename  = str_replace(array('/', '\\', '%'), '', $filename);
        $slugifier = new Slugifier();

        if (!empty($this->blacklistedExtensions)) {
            $filename = preg_replace('/\.('.join('|', $this->blacklistedExtensions).')$/', '.txt', $filename);
        }

        $parts    = pathinfo($filename);
        $filename = $slugifier->slugify($parts['filename']);
        $filename .= '.'.strtolower($parts['extension']);

        return sprintf(
            '%s/%s',
            $media->getUuid(),
            $filename
        );
    }

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public function createNew($data)
    {
        if ($data instanceof File) {
            /** @var $data File */

            $media = new Media();
            if (method_exists($data, 'getClientOriginalName')) {
                $media->setOriginalFilename($data->getClientOriginalName());
            } else {
                $media->setOriginalFilename($data->getFilename());
            }
            $media->setContent($data);

            $contentType = $this->mimeTypeGuesser->guess($media->getContent()->getPathname());
            $media->setContentType($contentType);

            return $media;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\File:show.html.twig';
    }

    /**
     * @return array
     */
    public function getAddFolderActions()
    {
        return array(
            FileHandler::TYPE => array(
                'type' => FileHandler::TYPE,
                'name' => 'media.file.add'
            )
        );
    }

}
