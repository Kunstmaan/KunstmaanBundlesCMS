# Data

Each vote has the following :

### Timestamp

The timestamp is filled in at PrePersist.

### Reference

The reference is a value by which the vote kan be grouped together. This can be the URL for Facebook Likes, an unique ID referring to a certain blog post, a certain category, ...

### Meta

This field is available to you to include a meta entity containing additional information (user info, ...).

### IP

The IP address of the user triggering the voting event.

### Value

Value is by default 1.

In most cases the default value of 1 is all you need. But in a few cases you may want to give your vote a specific value. For example, you track Facebook Likes and for each each like you commit yourself to donate €0.10 to sharity. This means you can give a vote a value of 10. When requesting the sum of the your total vote value, say you have 283 votes, which means a value of 2830. Divide it by 100 (100 cents in 1 euro) and you'll get the amount you committed yourself donate to sharity : €28,3.

# Configuration

You can override the default values for each type of vote in your config.yml

```YAML
kunstmaan_voting:
    actions:
        facebook_like:
            default_value: %kuma_voting_default_value%
        facebook_send:
            default_value: %kuma_voting_default_value%
        linkedin_share:
            default_value: %kuma_voting_default_value%
```

## Custom action and parameters

You can add your own action to it and even add additional parameters to the existing or your own action :

```YAML
kunstmaan_voting:
    actions:
        facebook_like:
            default_value: 2
        facebook_send:
            default_value: %kuma_voting_default_value%
        linkedin_share:
            default_value: 5
        yourownvote:
            default_value: 10
            other_param: "some_value"
```

You can retrieve these values the following way :

```PHP
$actions = $this->container->getParameter('kuma_voting.actions');
if (isset($actions['yourownvote'])) {
    $vote->setValue($actions['yourownvote']['default_value']);
    $other_param = $actions['yourownvote']['other_param'];
}
```

# Supported services

## Facebook

* Like
* Send

See : [Facebook documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/facebook.md)

## LinkedIn

* Share

See : [LinkedIn documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/linkedin.md)