# Node version lock implementation in NodeBundle

The KunstmaanNodeBundle implements the [Symfony CMF](http://cmf.symfony.com/) [RoutingExtra](https://github.com/symfony-cmf/RoutingExtraBundle) bundle.

## Lock?

When you edit a node, and at the same time someone else edits the node, the node of the user who does the last save will be kept.
With this new feature a message will be shown when someone else is editing the same page.
When saving the node, a new version of the page will be saved.

## Implementation

Enable this feature by using:

kunstmaan_node:
    lock:
        enabled: true
        threshold: 35 #seconds, optional
        check_interval: 15 #seconds, optional


