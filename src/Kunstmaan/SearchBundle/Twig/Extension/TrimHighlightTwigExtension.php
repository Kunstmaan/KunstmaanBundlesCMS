<?php

namespace Kunstmaan\SearchBundle\Twig\Extension;

/**
 * A Twig extension for highlighting words in text
 */
class TrimHighlightTwigExtension extends \Twig_Extension
{
  /**
   * @var \Twig_Environment
   */
  protected $environment;

  /**
   * {@inheritdoc}
   */
  public function initRuntime(\Twig_Environment $environment)
  {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters()
  {
    return array(
      'trim_highlight'  => new \Twig_Filter_Method($this, 'trimhighlight')
    );
  }

  /**
   * @param string $sentence
   *
   * @return string
   */
  public function trimhighlight($sentence)
  {
    $trimmedSpaces = ltrim($sentence);
    $trimmed = ltrim($trimmedSpaces, ".");

    return $trimmed;
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'trimhightlight_twig_extension';
  }
}

