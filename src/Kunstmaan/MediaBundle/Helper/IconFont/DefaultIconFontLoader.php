<?php

namespace Kunstmaan\MediaBundle\Helper\IconFont;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * DefaultIconFontLoader
 */
class DefaultIconFontLoader extends AbstractIconFontLoader
{
    /**
     * @var string
     */
    private $cssPath;

    /**
     * @param array $data
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     */
    public function setData(array $data)
    {
        if (!array_key_exists('css', $data)) {
            throw new MissingOptionsException('Missing required loader_data option: "css"');
        }

        $this->cssPath = trim($data['css'], '/');
        $pathInfo = pathinfo($this->cssPath);

        if ($pathInfo['extension'] !== 'css') {
            throw new InvalidOptionsException(sprintf('The loader data requires a valid css file. "%s" given', $pathInfo['extension']));
        }

        $cssPath = $this->rootPath . '/web/' . $this->cssPath;
        if (!file_exists($cssPath)) {
            throw new InvalidOptionsException(sprintf('Could not find the css file with this path "%s"', $cssPath));
        }
    }

    /**
     * @return string
     */
    public function getCssLink()
    {
        return '/' . $this->cssPath;
    }

    /**
     * @return array
     */
    public function getCssClasses()
    {
        $contents = file_get_contents($this->rootPath . '/web/' . $this->cssPath);

        preg_match_all('/\.([a-zA-Z0-9-_]+):before[ ]*\{[ \n]*content:/', $contents, $matches);

        return $matches[1];
    }
}
