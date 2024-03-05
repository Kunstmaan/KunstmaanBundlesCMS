<?php

namespace Kunstmaan\MediaBundle\Twig;

use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
use Kunstmaan\MediaBundle\Helper\ManipulateImageService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @experimental This feature is experimental and is a subject to change, be advised when using this feature and classes.
 */
final class MediaTwigExtension extends AbstractExtension
{
    /** @var ManipulateImageService */
    private $manipulateImageService;

    public function __construct(ManipulateImageService $manipulateImageService)
    {
        $this->manipulateImageService = $manipulateImageService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cropped_imagine_filter', [$this, 'getCroppedImage']),
            new TwigFunction('get_focus_point_class', [$this, 'getFocusPointClass']),
        ];
    }

    public function getCroppedImage(EditableMediaWrapper $editableMediaWrapper, string $view = '', ?string $filter = null)
    {
        if ($filter) {
            return $this->manipulateImageService->cropImage($editableMediaWrapper, $view, $filter);
        }

        return $this->manipulateImageService->cropImage($editableMediaWrapper, $view);
    }

    public function getFocusPointClass(EditableMediaWrapper $editableMediaWrapper, string $view = '')
    {
        return $this->manipulateImageService->getFocusPointClass($editableMediaWrapper, $view);
    }
}
