# Setup the Analytics Dashboard
Requirements:

* Google Analytics (GA) account
* Registered website (property) on GA
* Google developers console account

## Account setup

### Google developers console

We’ll start creating a developers console account. This account is used to communicate with the Google APIs, and stores all credentials. We’ll need these credentials in our app. You can skip this step if you already have an existing account set up.

Go to [http://cloud.google.com/console](http://cloud.google.com/console) and log in with a Google account. You can use an existing account or create a new one especially for your app. Start by creating a new project. Once the project is created, you’ll have to enable access for the Analytics API. Go to the APIs & Auth section, and select the APIs tab. Search the list for the Analytics API, and enable it by pressing the button on the right.

Now go to the Credentials tab in the APIs & Auth section. You’ll have to create both a client ID and an API key. Start with the client ID, select “web application” and press create. We’ll add an authorized redirect URI later. Now create a new key and choose “browser key”. Leave the whitelist blank and press “create”. Be sure to keep the console open for a while.

Google is often working on it's cloud admin section, recently we have been getting 401 errors complaining the application name was empty. for some reason under "Consent Screen" the application name was indeed empty. Fill it in and you are good to go!

### Google analytics

Next up is a Google Analytics (GA) account. You can use the same account as used in the developers console, create a new one, or use an existing one. If you’ve already got a GA account and a web property set up, you can skip this step.

In GA, set up a new property: go to the admin section, open the dropdown in the “property” column and create a new one. After filling in all the required fields, press the “get tracking ID” button. You’ll see a code snippet with some javascript, this has to be copied into the section of your app.

## Website setup

### Starting from a clean install

If you’re starting a new project with the Kunstmaan bundles, everything is already in place if you’ve followed the [Getting Started guide](http://bundles.kunstmaan.be/getting-started), and you can skip this step.

### Update an existing website

Add following parameters to `app/config/parameters.yml.dist`

    google.api.client_id: ''
    google.api.client_secret: ''
    google.api.dev_key: ''

Add the dashboard bundle to `composer.json`

    "require": {
	...
	"kunstmaan/dashboard-bundle": "~3.0",
	...
    }

Add the bundle in `app/AppKernel.php`

	$bundles = array(
	    ...
	    new Kunstmaan\DashboardBundle\KunstmaanDashboardBundle(),
	    ...
	);

Add bundle routing in `app/config/routing.yml`

    kunstmaan_dashboard:
	resource: "@KunstmaanDashboardBundle/Resources/config/routing.yml"
	prefix:   /{_locale}/
	requirements:
	    _locale: %requiredlocales%

Change dashboard route in `app/config/config.yml`

    kunstmaan_admin:
	dashboard_route: 'kunstmaan_dashboard'

Update composer

    composer update

Update the database, use either a migration or force a schema update

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate
    app/console doctrine:schema:update --force

## App setup

Navigate to the backend of your app. ( /app_dev.php/en/admin/ or something familiar). Log in (admin-admin or another account). You’ll see a short summary on how to set up the console credentials, search for the redirect URI between brackets in step 1. You’ll have to insert it into the developers console, so go back there, and edit the settings of the client ID you created in the first step. In the section “Authorized redirect URI”, you can paste this link. (...../admin/dashboard/widget/googleanalytics/setToken/). Note that you can add multiple redirect URIs if you’re using the same account for multiple apps, by just entering a new line for each.

Now it’s time to configure the parameters.yml file in app/config/parameters.yml. You’ll see three parameters with empty values:

* **google.api.client_id: ''** The client ID from the OAuth 2.0 Credentials in the Google Developers console.
* **google.api.client_secret: ''** The client secret from the OAuth 2.0 Credentials in the Google Developers console.
* **google.api.dev_key: ''** The API key from the public API Credentials in the Google Developers console.
After you’ve entered these credentials, save the parameters.yml file, and refresh the admin page in the backend. You can now connect your GA account.

## Connecting to Google Analytics

### Granting permission

We’ll need a token from Google, which grants access to your GA data. After pressing the connect button, you’ll be redirected to a Google page, asking you to log in (if you’re not logged in yet), and accept the request for permission to view your analytics data. Note that you should use the account which you used in GA. This is probably the same though as your developers console account.

After pressing “accept’, you’ll be redirected again to the admin backend.

### First time collecting the data

On the next page, you’ll be asked to select the website you want to track and which profile on the next one. Note that loading all the available websites can take a while, so just be patient when "stuck" on the previous screen, it will load after a while. After you’ve saved these settings, the dashboard will be loaded. However, it still has no data in it. Just press the update button to update your data.

You can also load the data from the console with the command

    app/console kuma:dashboard:collect

You can always update the data yourself this way, but it’s easier to configure a cronjob to do this every 30 minutes, or another interval.