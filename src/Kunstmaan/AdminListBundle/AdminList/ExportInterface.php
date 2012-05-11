<?php

namespace Kunstmaan\AdminListBundle\AdminList;

interface ExportInterface {

    public function getUrl();

    public function getLabel();

    public function getIcon();

    public function getTemplate();

}
