<?php

namespace Kunstmaan\SearchBundle\Configuration;

/**
 * Interface for a SearchConfiguration
 */
interface SearchConfigurationInterface
{
    /**
     * Create indexes
     */
    public function create();

    /**
     * Populate the indexes
     */
    public function index();

    /**
     * Delete indexes
     */
    public function delete();



}
