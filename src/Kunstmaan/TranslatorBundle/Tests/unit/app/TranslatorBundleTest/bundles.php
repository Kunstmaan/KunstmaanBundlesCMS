<?php

return [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new Kunstmaan\UtilitiesBundle\KunstmaanUtilitiesBundle(),
    new \Kunstmaan\AdminBundle\KunstmaanAdminBundle(),
    new \Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle(),
    new \Symfony\Bundle\AclBundle\AclBundle(),
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
];
