<?php

namespace Kunstmaan\SearchBundle\Configuration;

interface SearchConfigurationInterface
{
    /**
     * Create indexes
     */
    public function createIndex();

    /**
     * Populate the indexes
     */
    public function populateIndex();

    /**
     * Delete indexes
     */
    public function deleteIndex();

    /**
     * This method returns an array of languages that are not analyzed.
     *
     * @return array
     */
    public function getLanguagesNotAnalyzed();
}
