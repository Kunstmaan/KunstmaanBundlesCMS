<?php

return [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Kunstmaan\UtilitiesBundle\KunstmaanUtilitiesBundle(),
    new \Kunstmaan\AdminBundle\KunstmaanAdminBundle(),
    new \Kunstmaan\AdminListBundle\KunstmaanAdminListBundle(),
    new \Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle(),
    new \Symfony\Bundle\AclBundle\AclBundle(),
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
];
