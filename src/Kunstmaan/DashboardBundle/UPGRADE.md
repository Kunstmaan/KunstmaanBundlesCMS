Upgrade Instructions
====================

## New installation

Follow the [Setup guide](Resources/doc/DashboardAnalyticsWidgetSetup.md)


## Upgrading from v2.3.6 or lower

Because of the changes with the multi-config setup, you'll need to flush your database and refill it.

    app/console doctrine:schema:update --force
    app/console kuma:dashboard:widget:googleanalytics:config:flush
    app/console kuma:dashboard:collect
