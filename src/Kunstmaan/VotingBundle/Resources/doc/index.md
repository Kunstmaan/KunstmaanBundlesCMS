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

# Supported services

## Facebook

* Like
* Send

See : [Facebook documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/facebook.md)

## LinkedIn

* Share (planned)
