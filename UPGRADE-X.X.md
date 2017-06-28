# UPGRADE FROM 3.6 to X.X

## [PagePartBundle] Add pagepart to page view changed
New ui implemented in the backend to add pageparts to a
page. This new UI is opt-in because we do not want to force older projects to
have to do the extra work required to make this new ui look good.
It is possible to configure your pageparts to display a preview image of how
the pagepart will look when added to the page. You can find more information
about enabling and configuring this new view in the README of the
PagePartBundle.

