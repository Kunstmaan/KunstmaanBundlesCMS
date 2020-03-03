<?php

namespace Kunstmaan\MediaBundle\Twig;

use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Helper\ManipulateImageService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaTwigExtension extends AbstractExtension
{
    /** @var ManipulateImageService */
    private $manipulateImageService;

    public function __construct(
        ManipulateImageService $manipulateImageService
    ) {
        $this->manipulateImageService = $manipulateImageService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'cropped_imagine_filter',
                [$this, 'getCroppedImage']
            ),
        ];
    }

    public function getCroppedImage(CroppableMediaLink $croppableMediaLink, string $view = '', string $filter = null)
    {
        if ($filter) {
            return $this->manipulateImageService->manipulateOnTheFly($croppableMediaLink, $view, $filter);
        }

        return $this->manipulateImageService->manipulateOnTheFly($croppableMediaLink, $view);
    }
}
