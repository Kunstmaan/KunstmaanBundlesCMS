<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
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

    public function getFocusPointClass(EditableMediaWrapper $editableMediaWrapper, string $view = ''): string
    {
        if (null === $editableMediaWrapper->getRunTimeConfig()) {
            return '';
        }

        $runTimeConfig = json_decode($editableMediaWrapper->getRunTimeConfig(), true);

        if (!is_array($runTimeConfig) || empty($view) || !isset($runTimeConfig[$view]['class'])) {
            return '';
        }

        return $runTimeConfig[$view]['class'];
    }

    public function cropImage(EditableMediaWrapper $editableMediaWrapper, string $view = '', string $filter = 'optim'): string
    {
        $media = $editableMediaWrapper->getMedia();
        $filename = $media->getOriginalFilename();
        $filename = str_replace(['/', '\\', '%'], '', $filename);

        $parts = pathinfo($filename);
        $filename = $this->slugifier->slugify($parts['filename']);
        if (\array_key_exists('extension', $parts)) {
            $filename .= '.' . strtolower($parts['extension']);
        }

        $path = sprintf(
            'uploads/media/%s/%s',
            $media->getUuid(),
            $filename
        );

        $runTimeConfigForView = [];
        if ($editableMediaWrapper->getRunTimeConfig() !== null) {
            $runTimeConfig = json_decode($editableMediaWrapper->getRunTimeConfig(), true);

            if (
                is_array($runTimeConfig)
                && !empty($view)
                && isset($runTimeConfig[$view]['start'], $runTimeConfig[$view]['size'])
            ) {
                $runTimeConfigForView = [
                    'crop' => [
                        'start' => $runTimeConfig[$view]['start'],
                        'size' => $runTimeConfig[$view]['size'],
                    ],
                ];
            }
        }

        try {
            return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, $filter, $runTimeConfigForView);
        } catch (\Exception $exception) {
            $runTimeConfigForView = [];

            return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, $filter, $runTimeConfigForView);
        }
    }
}
