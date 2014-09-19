Upgrade Instructions
====================

## New installation

Follow the [Setup guide](Resources/doc/DashboardAnalyticsWidgetSetup.md)


## Upgrading from v2.3.6 or lower

Because of the changes with the multi-config setup, you'll need to flush your database and refill it.

Use either a migration or force a schema update to update the database

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate
    app/console doctrine:schema:update --force

Flush the data and reload it

    app/console kuma:dashboard:widget:googleanalytics:data:flush
    app/console kuma:dashboard:collect
