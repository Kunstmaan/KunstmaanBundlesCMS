<?php

namespace Kunstmaan\SearchBundle\Search;

class LanguageAnalysisFactory extends AbstractAnalysisFactory
{
    /**
     * @param string $language
     *
     * @return AnalysisFactoryInterface
     */
    public function addIndexAnalyzer($language)
    {
        $this->analyzers['default'] = array(
            'type' => $language,
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
            'type' => $language,
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
}
