<?php

namespace Kunstmaan\SearchBundle\Configuration;

/**
 * Interface for a SearchConfiguration.
 */
interface SearchConfigurationInterface
{
    /**
     * Create indexes.
     */
    public function createIndex();

    /**
     * Populate the indexes.
     */
    public function populateIndex();

    /**
     * Delete indexes.
     */
    public function deleteIndex();
}
