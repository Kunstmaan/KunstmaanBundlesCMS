<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Gaufrette\Filesystem;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\File\FileType;
use Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactoryInterface;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypesInterface;

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
    public $mediaPath;

    /**
     * @var Filesystem
     */
    public $fileSystem;

    /**
     * @deprecated This property is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the `$mimeTypes` property instead.
     *
     * @var MimeTypeGuesser
     */
    public $mimeTypeGuesser;

    /**
     * @deprecated This property is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the `$mimeTypes` property instead.
     *
     * @var ExtensionGuesser
     */
    public $extensionGuesser;

    /** @var MimeTypesInterface */
    private $mimeTypes;

    /**
     * Files with a blacklisted extension will be converted to txt
     *
     * @var array
     */
    private $blacklistedExtensions = [];

    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param int                                                $priority
     * @param MimeTypeGuesserFactoryInterface|MimeTypesInterface $mimeTypes
     * @param ExtensionGuesserFactoryInterface                   $extensionGuesserFactoryInterface
     */
    public function __construct($priority, /*MimeTypesInterface*/ $mimeTypes, ExtensionGuesserFactoryInterface $extensionGuesserFactoryInterface = null)
    {
        parent::__construct($priority);

        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$mimeTypes instanceof MimeTypesInterface && !$mimeTypes instanceof MimeTypeGuesserFactoryInterface) {
            throw new \InvalidArgumentException(sprintf('The "$mimeTypes" argument must implement the "%s" or "%s" interface', MimeTypesInterface::class, MimeTypeGuesserFactoryInterface::class));
        }

        if (null !== $extensionGuesserFactoryInterface) {
            @trigger_error(sprintf('Passing a value for "$extensionGuesserFactoryInterface" in "%s" is deprecated since KunstmaanMediaBundle 5.7 and this parameter will be removed in KunstmaanMediaBundle 6.0.', __METHOD__), E_USER_DEPRECATED);
        }

        if ($mimeTypes instanceof MimeTypeGuesserFactoryInterface) {
            @trigger_error(sprintf('Passing an instance of "%s" for "$mimeTypes" in "%s" is deprecated since KunstmaanMediaBundle 5.7 and this parameter will be removed in KunstmaanMediaBundle 6.0. Inject the an instance of "%s" instead.', MimeTypeGuesserFactoryInterface::class, __METHOD__, MimeTypesInterface::class), E_USER_DEPRECATED);

            $this->mimeTypeGuesser = $mimeTypes->get();
        } else {
            $this->mimeTypes = $mimeTypes;
        }

        if ($extensionGuesserFactoryInterface instanceof ExtensionGuesserFactoryInterface) {
            $this->extensionGuesser = $extensionGuesserFactoryInterface->get();
        }
    }

    public function setSlugifier(SlugifierInterface $slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * Inject the blacklisted
     */
    public function setBlacklistedExtensions(array $blacklistedExtensions)
    {
        $this->blacklistedExtensions = $blacklistedExtensions;
    }

    /**
     * Inject the path used in media urls.
     *
     * @param string $mediaPath
     */
    public function setMediaPath($mediaPath)
    {
        $this->mediaPath = $mediaPath;
    }

    public function setFileSystem(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'File Handler';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return FileHandler::TYPE;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return FileType::class;
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
     * @return FileHelper
     */
    public function getFormHelper(Media $media)
    {
        return new FileHelper($media);
    }

    /**
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

        $contentType = $this->guessMimeType($content->getPathname());
        if ($content instanceof UploadedFile) {
            $pathInfo = pathinfo($content->getClientOriginalName());

            if (!\array_key_exists('extension', $pathInfo)) {
                $pathInfo['extension'] = $this->getExtensions($contentType);
            }

            $media->setOriginalFilename($this->slugifier->slugify($pathInfo['filename']) . '.' . $pathInfo['extension']);
            $name = $media->getName();

            if (empty($name)) {
                $media->setName($media->getOriginalFilename());
            }
        }

        $media->setContentType($contentType);
        $media->setFileSize(filesize($media->getContent()));
        $media->setUrl($this->mediaPath . $this->getFilePath($media));
        $media->setLocation('local');
    }

    public function removeMedia(Media $media)
    {
        $adapter = $this->fileSystem->getAdapter();

        // Remove the file from filesystem
        $fileKey = $this->getFilePath($media);
        if ($adapter->exists($fileKey)) {
            $adapter->delete($fileKey);
        }

        // Remove the files containing folder if there's nothing left
        $folderPath = $this->getFileFolderPath($media);
        if ($adapter->exists($folderPath) && $adapter->isDirectory($folderPath) && !empty($folderPath)) {
            $allMyKeys = $adapter->keys();
            $everythingfromdir = preg_grep('/' . $folderPath, $allMyKeys);

            if (\count($everythingfromdir) === 1) {
                $adapter->delete($folderPath);
            }
        }

        $media->setRemovedFromFileSystem(true);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMedia(Media $media)
    {
        $this->saveMedia($media);
    }

    public function saveMedia(Media $media)
    {
        if (!$media->getContent() instanceof File) {
            return;
        }

        $originalFile = $this->getOriginalFile($media);
        $originalFile->setContent(file_get_contents($media->getContent()->getRealPath()));
    }

    /**
     * @return \Gaufrette\File
     */
    public function getOriginalFile(Media $media)
    {
        return $this->fileSystem->get($this->getFilePath($media), true);
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

            $contentType = $this->guessMimeType($media->getContent()->getPathname());
            $media->setContentType($contentType);

            return $media;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getShowTemplate(Media $media)
    {
        return '@KunstmaanMedia/Media/File/show.html.twig';
    }

    /**
     * @return array
     */
    public function getAddFolderActions()
    {
        return [
            FileHandler::TYPE => [
                'type' => FileHandler::TYPE,
                'name' => 'media.file.add',
            ],
        ];
    }

    /**
     * @return string
     */
    private function getFilePath(Media $media)
    {
        $filename = $media->getOriginalFilename();
        $filename = str_replace(['/', '\\', '%'], '', $filename);

        if (!empty($this->blacklistedExtensions)) {
            $filename = preg_replace('/\.(' . implode('|', $this->blacklistedExtensions) . ')$/', '.txt', $filename);
        }

        $parts = pathinfo($filename);
        $filename = $this->slugifier->slugify($parts['filename']);
        if (\array_key_exists('extension', $parts)) {
            $filename .= '.' . strtolower($parts['extension']);
        }

        return sprintf(
            '%s/%s',
            $media->getUuid(),
            $filename
        );
    }

    /**
     * @return string
     */
    private function getFileFolderPath(Media $media)
    {
        return substr($this->getFilePath($media), 0, strrpos($this->getFilePath($media), $media->getOriginalFilename()));
    }

    private function guessMimeType($pathName)
    {
        // NEXT_MAJOR: remove method and inline guessMimeType call
        if ($this->mimeTypeGuesser instanceof MimeTypeGuesser) {
            return $this->mimeTypeGuesser->guess($pathName);
        }

        return $this->mimeTypes->guessMimeType($pathName);
    }

    private function getExtensions($mimeType)
    {
        // NEXT_MAJOR: remove method and inline getExtensions call
        if ($this->extensionGuesser instanceof ExtensionGuesser) {
            return $this->extensionGuesser->guess($mimeType);
        }

        return $this->mimeTypes->getExtensions($mimeType)[0] ?? '';
    }
}
