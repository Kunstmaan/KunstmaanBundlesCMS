# UPGRADE FROM 4.* to 5.0

## Elastica

The ruflin/elastica bundle has been upgraded to the latest 5.1 version.
This version is compatible with the latest elasticsearch version.

The only change that should be made is when you override the NodeSearcher that comes by default from the bundles:

**The setMinimumNumberShouldMatch function is now replaced by the setMinimumShouldMatch function**

When you have created some extra extensions on elastica you should read the changelog:

https://github.com/ruflin/Elastica/blob/master/CHANGELOG.md