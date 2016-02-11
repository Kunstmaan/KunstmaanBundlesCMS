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

    /** @var array */
    private $stopwords;


    public function __construct()
    {
        $this->analyzers  = array();
        $this->tokenizers = array();
        $this->filters    = array();
        $this->stopwords  = array();
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
     * @param string $lang
     *
     * @return AnalysisFactoryInterface
     */
    public function addIndexAnalyzer($lang)
    {
        $this->analyzers['index_analyzer_' . $lang] = array(
            'type'      => 'custom',
            'tokenizer' => 'whitespace',
            'filter'    => array(
                'trim',
                'lowercase',
                'stopwords_' . $lang,
                'asciifolding',
                'strip_special_chars',
                'ngram'
            )
        );

        // add dependencies
        $this
            ->addStopWordsFilter($lang)
            ->addStripSpecialCharsFilter()
            ->addNGramFilter();

        return $this;
    }

    /**
     * @param string $lang
     *
     * @return AnalysisFactoryInterface
     */
    public function addSuggestionAnalyzer($lang)
    {
        $this->analyzers['suggestion_analyzer_' . $lang] = array(
            'type'      => 'custom',
            'tokenizer' => 'whitespace',
            'filter'    => array('trim', 'lowercase', 'asciifolding', 'strip_special_chars')
        );

        //add dependencies
        $this
            ->addStripSpecialCharsFilter()
            ->addNGramFilter();

        return $this;
    }

    /**
     * @return AnalysisFactoryInterface
     */
    public function addNGramFilter()
    {
        $this->filters['ngram'] = array(
            'type'     => 'nGram',
            'min_gram' => 4,
            'max_gram' => 30
        );

        return $this;
    }

    /**
     * @param string $lang
     * @param array  $words
     *
     * @return AnalysisFactoryInterface
     */
    public function addStopWordsFilter($lang, array $words = null)
    {
        if ($words === null && isset($this->stopwords[$lang])) {
            $words = $this->stopwords[$lang];
        } elseif ($words === null) {
            $words = array();
        }

        $this->filters['stopwords_' . $lang] = array(
            'type'        => 'stop',
            'stopwords'   => $words,
            'ignore_case' => true
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
     * @param string $lang
     * @param array  $stopwords
     *
     * @return AnalysisFactoryInterface
     */
    public function setStopwords($lang, $stopwords)
    {
        $this->stopwords[$lang] = $stopwords;

        return $this;
    }

    /**
     * @param string $lang
     *
     * @return array
     */
    public function getStopwords($lang = 'en')
    {
        if (isset($this->stopwords[$lang])) {
            return $this->stopwords[$lang];
        }

        return array();
    }

    /**
     * @param string $lang
     *
     * @return AnalysisFactoryInterface
     */
    public function setupLanguage($lang = 'en')
    {
        $this
            ->addIndexAnalyzer($lang)
            ->addSuggestionAnalyzer($lang);

        return $this;
    }

    /**
     * @param array|string $languages
     *
     * @return AnalysisFactoryInterface
     */
    public function setupLanguages($languages)
    {
        if (is_string($languages)) {
            $languages = explode('|', $languages);
        }
        foreach ($languages as $lang) {
            $this->setupLanguage($lang);
        }

        return $this;
    }
}
