<?php

namespace Kunstmaan\SearchBundle\Search;

abstract class AbstractAnalysisFactory implements AnalysisFactoryInterface
{
    /** @var array */
    protected $analyzers;

    /** @var array */
    protected $tokenizers;

    /** @var array */
    protected $filters;

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
    public abstract function addIndexAnalyzer($language);

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public abstract function addSuggestionAnalyzer($language);


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
    public function addNGramTokenizer()
    {
        $this->tokenizers['kuma_ngram'] = array(
            'type'     => 'nGram',
            'min_gram' => 4,
            'max_gram' => 30,
            'token_chars' => [ "letter", "digit" ]
        );
        return $this;
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
            ->addNGramTokenizer()
            ->addStopWordsFilter($language)
            ->addStemmerFilter($language);

        return $this;
    }
}
