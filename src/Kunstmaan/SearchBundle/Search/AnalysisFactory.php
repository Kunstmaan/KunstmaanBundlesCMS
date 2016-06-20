<?php

namespace Kunstmaan\SearchBundle\Search;

class AnalysisFactory implements AnalysisFactoryInterface
{
    /** @var array */
    private $analyzers;

    /** @var array */
    private $tokenizers;

    /** @var array */
    private $filters;

    public function __construct()
    {
        $this->analyzers  = array();
        $this->tokenizers = array();
        $this->filters    = array();
    }

    /**
     * @return array
     */
    public function build()
    {
        $analysis = array(
            'analyzer'  => $this->analyzers,
            'tokenizer' => $this->tokenizers,
            'filter'    => $this->filters
        );

        return $analysis;
    }

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addIndexAnalyzer($language)
    {
        $this->analyzers['default'] = array(
            'type'      => $language,
            'tokenizer' => 'standard',
            'filter'    => array(
                'trim',
                'lowercase',
                'asciifolding',
                'strip_special_chars',
                $language . '_stop',
                $language . '_stemmer'
            )
        );

        return $this;
    }

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addSuggestionAnalyzer($language)
    {
        $this->analyzers['default_search'] = array(
            'type'      => $language,
            'tokenizer' => 'standard',
            'filter'    => array(
                'trim',
                'lowercase',
                'asciifolding',
                'strip_special_chars',
                $language . '_stop',
                $language . '_stemmer'
            )
        );

        return $this;
    }

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addStopWordsFilter($language)
    {
        $this->filters[$language . '_stop'] = array(
            'type'        => 'stop',
            'stopwords'   => '_' . $language . '_'
        );

        return $this;
    }

    /**
     *
     * @param string $language
     *
     * @return AnalysisFactoryInterface

     */
    public function addStemmerFilter($language)
    {
        $this->filters[$language . '_stemmer'] = array(
            'type'        => 'stemmer',
            'language'    => $language
        );

        return $this;
    }

    /**
     * @return AnalysisFactoryInterface
     */
    public function addStripSpecialCharsFilter()
    {
        $this->filters['strip_special_chars'] = array(
            'type'        => 'pattern_replace',
            'pattern'     => '[^0-9a-zA-Z]',
            'replacement' => ''
        );

        return $this;
    }

    /**
     * @return AnalysisFactoryInterface
     */
    public function addNGramFilter()
    {
        // Ngrams are not used in our default implementation
    }


    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function setupLanguage($language = 'english')
    {
        $this
            ->addIndexAnalyzer($language)
            ->addSuggestionAnalyzer($language)
            ->addStripSpecialCharsFilter()
            ->addStopWordsFilter($language)
            ->addStemmerFilter($language);

        return $this;
    }
}
