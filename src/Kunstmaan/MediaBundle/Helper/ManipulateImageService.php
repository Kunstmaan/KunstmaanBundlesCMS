<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Liip\ImagineBundle\Service\FilterService;

class ManipulateImageService
{
    /** @var Slugifier */
    private $slugifier;

    /** @var FilterService */
    private $filterService;

    public function __construct(Slugifier $slugifier, FilterService $filterService)
    {
        $this->slugifier = $slugifier;
        $this->filterService = $filterService;
    }

    public function manipulateOnTheFly(CroppableMediaLink $croppableMediaLink, string $view = '', string $filter = 'optim'): string
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

        $runTimeConfigForView = [];
        if ($croppableMediaLink->getRunTimeConfig() !== null) {
            $runTimeConfig = json_decode($croppableMediaLink->getRunTimeConfig(), true);

            if (is_array($runTimeConfig) && !empty($view) && isset($runTimeConfig[$view])) {
                $runTimeConfigForView = [
                    'crop' => [
                        'start' => $runTimeConfig[$view]['start'],
                        'size' => $runTimeConfig[$view]['size'],
                    ],
                ];
            }
        }

        return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, $filter, $runTimeConfigForView);
    }
}
