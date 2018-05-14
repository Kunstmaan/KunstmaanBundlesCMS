<?php

namespace Kunstmaan\SearchBundle\Search;

interface AnalysisFactoryInterface
{
    /**
     * @return array
     */
    public function build();

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addIndexAnalyzer($language);

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addSuggestionAnalyzer($language);

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addStopWordsFilter($language);

    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addStemmerFilter($language);

    /**
     * @return AnalysisFactoryInterface
     */
    public function addStripSpecialCharsFilter();

    /**
     * @return AnalysisFactoryInterface
     */
    public function addNGramTokenizer();

    /**
     * @param string $language
     */
    public function setupLanguage($lang = 'english');
}
