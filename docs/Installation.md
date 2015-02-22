Installation
============

Welcome to the Kunstmaan Bundles Standard Edition - a fully-functional CMS (content management system) based on
[Symfony][1] that you can use as a skeleton for your websites.

This document contains information on how to download, install, and start using the Kunstmaan Bundles CMS.

This guide assumes that you have read the [official Symfony documentation][2] and know the basics of creating
Symfony applications.


1) Minimal Requirements
-----------------------

Since we use the Symfony framework, the [minimal requirements of Symfony][3] apply, so:

- PHP needs to be a minimum version of PHP 5.3.9
- JSON needs to be enabled
- ctype needs to be enabled
- curl needs to be enabled
- imagick needs to be enabled (if you wish to use the KunstmaanMediaBundle)
- Your PHP.ini needs to have the date.timezone setting

Apart from these you will also need the following :

- [Node.js][4]
- [Sass][5]
- [Bower][6]
- [Grunt][7]
- UglifyJS
- UglifyCss
- a database server, preferrably [MySQL][8] 5.x (we haven't tried any other DB yet...)
- a web server...

To install the required dependencies (apart from the database and web server) :

 - On OS X (using [Homebrew][9]):

   ```
   brew install node
   gem install sass # maybe you need sudo...
   npm install -g bower
   npm install -g grunt
   npm install -g grunt-cli
   npm install -g uglify-js
   npm install -g uglifycss
   ```

 - On Linux ([Debian][10] / [Ubuntu][11]):

   ```
   sudo apt-get install node
   gem install sass # maybe you need sudo...
   npm install -g bower
   npm install -g grunt
   npm install -g grunt-cli
   npm install -g uglify-js
   npm install -g uglifycss
   ```

 - On Windows:

   We kindly suggest installing [VirtualBox][12], [VMware][13] or another virtualization platform and running a Linux
   VM on it, or basically : you're on your own.


2) Installing the Kunstmaan Bundles SE
--------------------------------------

As both the Kunstmaan Bundles and Symfony use [Composer][14] to manage their dependencies, the recommended way to
create a new project is to use Composer.

If you don't have Composer yet, download it following the instructions on [getcomposer.org/download][15].

Then, use the `create-project` command to generate a new Kunstmaan Bundles CMS application:

    composer create-project kunstmaan/bundles-standard-edition path/to/install

By using `-s dev`, you can get the latest master version:

    composer create-project kunstmaan/bundles-standard-edition path/to/install -s dev

Composer will install the Kunstmaan Bundles CMS and all its dependencies under the `path/to/install` directory, and
ask for some basic configuration settings (ie. database settings), you can leave the other settings at their default
values.


3) Checking and Configuring your Website
----------------------------------------

Before starting coding, make sure that your local system is properly configured for both Symfony and the Kunstmaan
Bundles.

Execute the `check.php` script from the command line:

    php app/check.php

The script returns a status code of `0` if all mandatory requirements are met, `1` otherwise.

Access the `config.php` script from a browser:

    http://localhost/path/to/app/web/config.php

If you get any warnings or recommendations, fix them before moving on. Don't click on any of the links on the page
yet though, since you'll get errors if you do...


4) Generating your Website
--------------------------

First, you should generate a Symfony bundle for your website.

    php app/console kuma:generate:bundle

This will ask for a name space, so simply enter something like `Sandbox\WebsiteBundle`. For all other
questions, the defaults should suffice.

Next, generate the default demo website setup.

    php app/console kuma:generate:default-site --demosite

This will ask for a bundle namespace (just leave that to the default) and for the prefix enter `sb_` (or enter one as
you see fit).

*NOTE:* When you are creating your own Kunstmaan Bundles based site and don't need the demo site fixtures, just leave
out the --demosite switch in the previous step.

When this is done, create the database (if you haven't done so already) and the schema.

    php app/console doctrine:database:create
    php app/console doctrine:schema:create

And finally, load the fixtures from the installed bundles:

    php app/console doctrine:fixtures:load

Just answer `Y` at the prompt.

To get started with Behat tests, you can generate basic tests for the admin interface by running the following:

    php app/console kuma:generate:admin-tests

Just accept the default Bundle Namespace at the prompt.

Now that all your code is generated, let's make sure all front-end assets are available:

    bower install
    npm install
    grunt build
    php app/console assets:install web
    php app/console assetic:dump

*NOTE*: You may have to run some of the commands above using sudo depending on your system setup...


5) Browsing the CMS Administration Pages
----------------------------------------

Congratulations! You're now ready to use the Kunstmaan Bundles CMS. Browse to:

    http://localhost/path/to/app/en/admin

And log in with user ```admin``` and password ```admin```.

*NOTE:* Make sure you change at least the password before putting your site online! You can do this by navigating to
Settings > Users in the back-end, and changing it there or by running the following:

    php app/console fos:user:change-password admin


6) Summary
----------

You should first check that your system setup matches the minimum requirements, but other than that here's the gist
of it:

    composer create-project kunstmaan/bundles-standard-edition path/to/install
    php app/console kuma:generate:bundle
    php app/console kuma:generate:default-site --demosite
    php app/console doctrine:schema:create
    php app/console doctrine:fixtures:load
    php app/console kuma:generate:admin-tests
    bower install
    npm install
    grunt build
    php app/console assets:install web
    php app/console assetic:dump

Enjoy!


[1]:  http://symfony.com/
[2]:  http://symfony.com/doc
[3]:  http://symfony.com/doc/current/reference/requirements.html
[4]:  http://nodejs.org/
[5]:  http://sass-lang.com/
[6]:  http://bower.io/
[7]:  http://gruntjs.com/
[8]:  http://www.mysql.com/
[9]:  http://brew.sh/
[10]: http://www.debian.org/
[11]: http://www.ubuntu.com/
[12]: http://www.virtualbox.org/
[13]: http://www.vmware.com/
[14]: https://getcomposer.org/
[15]: https://getcomposer.org/download
