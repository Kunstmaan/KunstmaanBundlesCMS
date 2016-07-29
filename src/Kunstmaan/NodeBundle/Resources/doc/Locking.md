# Node translation lock implementation in NodeBundle

The KunstmaanNodeBundle implements the [Symfony CMF](http://cmf.symfony.com/) [RoutingExtra](https://github.com/symfony-cmf/RoutingExtraBundle) bundle.

## Lock?

When you edit a node, and at the same time someone else edits the node, the node of the user who does the last save will be kept. With this new feature a messages
will be shown when someone else is editing the same page. When saving the node, there will be a new draft version instead of an edit of the public version.

## Implementation

Enable this feature by using:

kunstmaan_node:
    lock:
        enabled: true
        threshold: 1800 #seconds, optional
        
There is also a command that should be ran on cron for cleaning up those locks after the threshold is exceeded.

php bin/console kuma:nodes:clean-lock
        

