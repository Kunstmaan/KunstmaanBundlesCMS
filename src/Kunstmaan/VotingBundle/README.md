# KunstmaanVotingBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanVotingBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanVotingBundle)

A lot of sites enable users to vote or participate in actions where Facebook Likes are counted and rewarded. The KunstmaanVotingBundle was created to allow a faster setup of that kind of actions and will provide a backlog of votes your users casted. That way you can look for irregularities and automatically stop campaigns when their deadline has expired. It will provice support for votes on your site only but also for external social networks as Facebook so you can worry about you ideas and not how to implement it.

Installation requirements
-------------------------

You should be able to get Symfony 2.3 up and running before you can install the KunstmaanVotingBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/voting-bundle
```

Add the KunstmaanVotingBundle to your AppKernel.php file:

```PHP
new Kunstmaan\VotingBundle\KunstmaanVotingBundle(),
```

Add the KunstmaanVotingBundle to your routing.yml. Take your own routing into account, it's possible you will need to add the following code prior to your own routing configurations

```PHP
KunstmaanVotingBundle:
    resource: "@KunstmaanVotingBundle/Resources/config/routing.yml"
    prefix:   /
```

Overwrite the KunstmaanVotingBundle config to your needs in config.yml :

```PHP
kunstmaan_voting:
    actions:
        up_vote:
            default_value: 1
        down_vote:
            default_value: -1
        facebook_like:
            default_value: 1
        facebook_send:
            default_value: 1
        linkedin_share:
            default_value: 1
```

Use
---

* [Documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/VotingBundle.md)

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.
