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
                $language . '_stop',
                $language . '_stemmer',
                'asciifolding',
                'strip_special_chars'
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
                $language . '_stop',
                $language . '_stemmer',
                'asciifolding',
                'strip_special_chars'
            )
        );

        return $this;
    }
}
