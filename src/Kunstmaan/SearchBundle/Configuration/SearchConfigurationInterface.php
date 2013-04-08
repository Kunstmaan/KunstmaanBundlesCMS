<?php

namespace Kunstmaan\SearchBundle\Configuration;

interface SearchConfigurationInterface {

    public function create();

    public function index();

    public function delete();

}