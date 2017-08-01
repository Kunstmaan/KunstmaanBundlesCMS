# UPGRADE FROM 4.* to 5.0

## Elastica

The ruflin/elastica bundle has been upgraded to the latest 5.1 version.
This version is compatible with the latest elasticsearch version.

The only change that should be made is when you override the NodeSearcher that comes by default from the bundles:

**The setMinimumNumberShouldMatch function is now replaced by the setMinimumShouldMatch function**

When you have created some extra extensions on elastica you should read the changelog:

https://github.com/ruflin/Elastica/blob/master/CHANGELOG.md


## [PagePartBundle] Add pagepart to page view changed
New ui implemented in the backend to add pageparts to a
page. This new UI is opt-in because we do not want to force older projects to
have to do the extra work required to make this new ui look good.
It is possible to configure your pageparts to display a preview image of how
the pagepart will look when added to the page. You can find more information
about enabling and configuring this new view in the README of the
PagePartBundle.