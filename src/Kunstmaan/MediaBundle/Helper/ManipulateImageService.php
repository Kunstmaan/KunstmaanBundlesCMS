<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Service\FilterService;

/**
 * @experimental This feature is experimental and is a subject to change, be advised when using this feature and classes.
 */
final class ManipulateImageService
{
    /** @var Slugifier */
    private $slugifier;

    /** @var FilterService */
    private $filterService;

    /** @var FilterConfiguration */
    private $filterConfiguration;

    public function __construct(Slugifier $slugifier, FilterService $filterService, FilterConfiguration $filterConfiguration)
    {
        $this->slugifier = $slugifier;
        $this->filterService = $filterService;
        $this->filterConfiguration = $filterConfiguration;
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
        if (null === $media) {
            return '';
        }
        if (strpos($media->getContentType(), 'svg') !== false) {
            return $media->getUrl();
        }

        $path = $media->getUrl();
        $path = str_replace('://', '---', $path);

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
            $filterConfig = $this->filterConfiguration->get($filter);
        } catch (NonExistingFilterException $exception) {
            $filterConfig = $this->filterConfiguration->get('optim');
        }

        $oemConfig = null;
        if (isset($filterConfig['filters'])) {
            $oemConfig = $filterConfig;
            unset($filterConfig['filters']['crop']);
            $runTimeConfigForView = array_merge_recursive($runTimeConfigForView, $filterConfig['filters']);
            unset($filterConfig['filters']);
            $this->filterConfiguration->set($filter, $filterConfig);
        }

        try {
            $response = $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, $filter, $runTimeConfigForView);
        } catch (\Exception $exception) {
            $runTimeConfigForView = [];

            $response = $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, 'optim', $runTimeConfigForView);
        }

        if ($oemConfig !== null) {
            $this->filterConfiguration->set($filter, $oemConfig);
        }

        return $response;
    }
}
