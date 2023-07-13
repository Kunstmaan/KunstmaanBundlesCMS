<?php

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Kunstmaan\UtilitiesBundle\KunstmaanUtilitiesBundle;
use Kunstmaan\AdminBundle\KunstmaanAdminBundle;
use Kunstmaan\AdminListBundle\KunstmaanAdminListBundle;
use Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle;
use Symfony\Bundle\AclBundle\AclBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
return [
    new FrameworkBundle(),
    new SecurityBundle(),
    new TwigBundle(),
    new KunstmaanUtilitiesBundle(),
    new KunstmaanAdminBundle(),
    new KunstmaanAdminListBundle(),
    new KunstmaanTranslatorBundle(),
    new AclBundle(),
    new DoctrineBundle(),
];
