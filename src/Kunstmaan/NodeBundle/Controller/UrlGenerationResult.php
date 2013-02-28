<?

namespace Kunstmaan\NodeBundle\Controller;

class UrlGenerationResult {

    public function __construct($modified, $originalSlug, $originalUrl) {
        $this->slugModified = $modified;
        $this->originalSlug = $originalSlug;
        $this->originalUrl = $originalUrl;
    }

    /**
     * @return boolean If this is true the slug was modified and you can decide to inform the user or not.
     *                 The new slug and URL are set on the NodeTranslation.
     */
    protected $slugModified;
    /**
     * @return string The original slug.
     */
    protected $originalSlug;
    /**
     * @return string The original Url.
     */
    protected $originalUrl;

    public function getSlugModified()
    {
        return $this->slugModified;
    }

    public function setSlugModified($value)
    {
        $this->slugModified = $value;
    }

    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    public function getOriginalSlug()
    {
        return $this->originalSlug;
    }


    public function __toString()
    {
        return 'slugModified: ' . $this->getSlugModified() . ' originalSlug: ' . $this->getOriginalSlug() . ' originalUrl: ' . $this->getOriginalUrl();
    }
}
