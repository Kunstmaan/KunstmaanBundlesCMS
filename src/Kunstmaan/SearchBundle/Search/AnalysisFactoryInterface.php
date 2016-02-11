<?php

namespace Kunstmaan\SearchBundle\Search;

interface AnalysisFactoryInterface 
{
    /**
     * @return array
     */
    public function build();

    /**
     * @param string $lang
     *
     * @return AnalysisFactory
     */
    public function addIndexAnalyzer($lang);

    /**
     * @param string $lang
     *
     * @return AnalysisFactory
     */
    public function addSuggestionAnalyzer($lang);

    /**
     * @return AnalysisFactory
     */
    public function addNGramFilter();

    /**
     * @param string $lang
     * @param array  $words
     *
     * @return AnalysisFactory
     */
    public function addStopWordsFilter($lang, array $words = null);

    /**
     * @return AnalysisFactory
     */
    public function addStripSpecialCharsFilter();

    /**
     * @param string $lang
     * @param array  $stopwords
     */
    public function setStopwords($lang, $stopwords);

    /**
     * @param string $lang
     *
     * @return array
     */
    public function getStopwords($lang = 'en');

    /**
     * @param string $lang
     */
    public function setupLanguage($lang = 'en');

    /**
     * @param array|string $languages
     */
    public function setupLanguages($languages);
}
