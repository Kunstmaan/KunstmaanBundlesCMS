<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\Image;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Image\ImageHandler;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypes;

class ImageHandlerTest extends TestCase
{
    public function testPrepareWithSvg()
    {
        $media = new Media();
        $media->setContent(new File(__DIR__ . '/../../Fixtures/sample.svg'));

        $handler = new ImageHandler(1, new MimeTypes(), null);
        $handler->setSlugifier(new Slugifier());

        $handler->prepareMedia($media);

        $this->assertSame(['original_width' => null, 'original_height' => null], $media->getMetadata());
    }
}
