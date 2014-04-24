Upgrade Instructions
====================

## Using the Google Analytics dashboard

The Google Analytics dashboard depends on three parameters added in app/config/parameters.yml. Because these parameters are injected, there is no way to catch an exception if they are not present. This is a BC breaking change, and can be fixed be just adding the three parameters and default values '' to parameters.yml.

To upgrade, first pull in the new version of all bundles, then add these parameters:

    google.api.client_id: ''
    google.api.client_secret: ''
    google.api.client_secret: ''

Now you can run the app without any errors, follow the documentation (DashboardBundle/resources/doc/SetupAnalyticsDashboard.md) to configure the dashboard.
