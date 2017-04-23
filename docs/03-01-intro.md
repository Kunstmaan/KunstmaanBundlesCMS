# Installation

> For the sake of clarity we will perform this installation in a preconfigured Vagrant box built with [PuPHPet](https://puphpet.com). More info in [this chapter about the PuPHPet development environment](./03-02-development-environment.md). If you have a development environment, just adjust to match your specifics.
>
> This chapter assumes you have started the box (`vagrant up`), ssh'ed into it (`vagrant ssh`) and have navigated to the `/var/www` folder (`cd /var/www`). If there is a folder there called `myprojectname` you should delete it by running: `rm -Rf myprojectname`

## Downloading and configuring the base CMS

We will get started by downloading the Kunstmaan Bundles Standard Edition to get the CMS and all it's dependencies.

```sh
composer create-project kunstmaan/bundles-standard-edition myprojectname
```

![composer create](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/composer-create.png)


It will then ask you some questions to configure Symfony and the CMS system. At this point just fill in the database_name like so:

![composer parameters](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/composer-parameters.png)

> Since our project is named myproject, the websitetitle, session_prefix, searchindexname and searchindexprefix are all ok. In a real project they probably aren't.

We will configure all other parameters later on.

First step now is to add all these files into version control. What version control system and what vcs hosting you use is up to you. This example assumes [Git](http://git-scm.com) and [GitHub](https://github.com).

Create a new repository (in most cases a private one). Don't add any files from the GitHub interface to start with.

![composer create-project -s dev kunstmaan/bundles-standard-edition myprojectname](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/github.png)

Then execute these commands in `/var/www/myprojectname/` to initialise the git repository

```
git init
git add .
git commit -m "Clean install of the KunstmaanBundlesCMS"
git remote add origin https://github.com/USERNAME/MyProject.git
git push -u origin master
```

At this point refreshing the page for your repository on GitHub will show you your files.

> Please note that the [.gitignore](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/.gitignore#L4) file of the KunstmaanBundlesStandardEdition prevents committing your parameters.yml file into git. Depending on your needs, you could change this by removing that line from yout .gitignore file.

## Generating a bundle

First, you should generate a bundle for your website specific code.

```
bin/console kuma:generate:bundle
```

Each bundle is hosted under a namespace (like Acme/WebsiteBundle). The namespace should begin with a "vendor" name like your company name, your project name, or your client name, followed by one or more optional category sub-namespaces, and it should end with the bundle name itself (which must have Bundle as a suffix).

See this [Symfony Best Practices document](http://symfony.com/doc/current/cookbook/bundles/best_practices.html#index-1) for more details on bundle naming conventions. At Kunstmaan we use `ClientName/WebsiteBundle` as a convention for a standard CMS project. In this example we use `MyProject/WebsiteBundle`.

For all other questions, the defaults should suffice.

> Sometimes there are some issues with bash/zsh escaping in terminal input, so use / instead of \ for the namespace delimiter to avoid any problems.

![bin/console kuma:generate:bundle](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/bundlegen.png)

## Generating your website skeleton

Now that we have a bundle to store our code in, we are going to generate the skeleton for our website. You do this by running the following command. It will ask you for a MySQL database prefix, just leave it unless you have a specific reason to do so.

```
bin/console kuma:generate:default-site
```

![bin/console kuma:generate:default-site](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/defaultsitegen.png)

This generates:

* A Groundcontrol configuration ~ gulpfile.babel.js. [Groundcontrol is a build tool based on gulp to automate and enhance mostly front-end development workflows](http://gulpjs.com)
* A Bundler configuration ~ Gemfile. [Bundler is a RubyGems package manager we use mostly for getting specific versions of Hologram](http://bundler.io).
* A basic selection of user interface elements, sass files, etc
* A barebones selection of controllers, entities, twig files, pageparts, etc
* The needed fixtures to setup the CMS

> This is the best starting point for a real website. If you add `--demosite` to the command above, this generator will generate more styling and fixtures so that the result ends up exactly like [the demo site](http://demo.bundles.kunstmaan.be).

## Initialising the database

When this is done, create the database schema and load all the generated fixtures to fill it.

```
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load
```

![bin/console doctrine:schema:create](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/schemacreate.png)

## Generate Unit and Behat tests (optional)

If you want, you can generate a set of Unit and Behat test features that test that your site is working correctly. It will test logging in to the administration interface, it will create a page and tries to enter every pagepart. Most generators will generate extra features when you add features later on in development.

```
bin/console kuma:generate:admin-tests
```

Just accept the default bundle namespace at the prompt.

![bin/console kuma:generate:admin-tests](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/behattests.png)


## Get all the front-end CMS assets

Now that all your code is generated, let's make sure all front-end assets are available.

First make sure you have [UglifyCSS](https://github.com/fmarcia/UglifyCSS) and [UglifyJS](http://lisperator.net/uglifyjs/) installed globally.

> UglifyCSS and UglifyJS are used via [Assetic](https://github.com/kriswallsmith/assetic) to minimize the javascript and css files in the administration interface, as per [this recipe on in the Symfony Cookbook: How to Minify CSS/JS Files (Using UglifyJS and UglifyCSS)](http://symfony.com/doc/current/cookbook/assetic/uglifyjs.html)

Then execute the following commands:

```
bin/console assets:install --symlink
bin/console assetic:dump
```

At this point browsing to [http://kunstmaan.cms/en/admin](http://kunstmaan.cms/en/admin) should greet you with the following screens.

![Demo Site Admin](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/demositeadmin.png)

> Note that the screenshots were made of a site using the `--demosite` option during generation


## Get all the front-end website assets (KuMa workflow)

First make sure you installed [node.js](https://node.js.org/) v7.7 (or higher).
You can test your node version by running `node -v`.
> Node comes with npm installed so you should have a version of npm. However, npm gets updated more frequently than Node does, so you'll want to make sure it's the latest version.
>
> ```npm install npm@latest -g```
>
> Test: Run `npm -v`. The version should be higher than 2.1.8.

Then execute the following commands:

```
bundle install
npm install
npm run build
```

At this point browsing to [http://kunstmaan.cms](http://kunstmaan.cms) should show you the website with minimalistic styling.
=======
>>>>>>> 4.0
