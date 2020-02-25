<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Liip\ImagineBundle\Service\FilterService;

class ManipulateImageService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Slugifier */
    private $slugifier;

    /** @var FilterService */
    private $filterService;

    public function __construct(EntityManagerInterface $em, Slugifier $slugifier, FilterService $filterService)
    {
        $this->slugifier = $slugifier;
        $this->filterService = $filterService;
        $this->em = $em;
    }

    public function manipulateOnTheFly(CroppableMediaLink $croppableMediaLink, ?array $runTimeConfig, string $view = ''): string
    {
        /** @var Media $media */
        $media = $croppableMediaLink->getMedia();
        $filename = $media->getOriginalFilename();
        $filename = str_replace(['/', '\\', '%'], '', $filename);

        $parts = pathinfo($filename);
        $filename = $this->slugifier->slugify($parts['filename']);
        if (\array_key_exists('extension', $parts)) {
            $filename .= '.'.strtolower($parts['extension']);
        }

        $path = sprintf(
            'uploads/media/%s/%s',
            $media->getUuid(),
            $filename
        );

        $serializedRunTimeConfig = $runTimeConfig !== null ? serialize($runTimeConfig) : null;
        if ($serializedRunTimeConfig !== $croppableMediaLink->getRunTimeConfig()) {
            $croppableMediaLink->setRunTimeConfig($serializedRunTimeConfig);
            $this->em->flush();
        }

        $runTimeConfigForView = [];
        if(is_array($runTimeConfig) && !empty($view) && isset($runTimeConfig[$view])) {
            $runTimeConfigForView = $runTimeConfig[$view];
        }

        return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, 'optim', $runTimeConfigForView);
    }
}
