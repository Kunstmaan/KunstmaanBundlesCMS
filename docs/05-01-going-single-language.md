Going single language
=====================

The Standard Edition installs a multilanguage site. This is important for us because Belgium is a multilingual country, but it's clear that there are a lot of cases where it is not needed. While the CMS will always retain it multilingual capabilities, we want to remove the language from the URL.

## 1) Change the routing.yml

Switch out the default [routing.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/routing.yml) with [routing.singlelang.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/routing.singlelang.yml)

```
mv app/config/routing.yml app/config/routing.multilang.yml
mv app/config/routing.singlelang.yml app/config/routing.yml
```

*WARNING: If you generated bundles before going single language, check that you have all routing moved over to the new routing file!*

## 2) Change the security.yml

Switch out the default [security.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/security.yml) with [security.singlelang.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/security.singlelang.yml)

```
mv app/config/security.yml app/config/security.multilang.yml
mv app/config/security.singlelang.yml app/config/security.yml
```

## 3) Change your AppKernel

Comment out the `KunstmaanLanguageChooserBundle` and `LuneticsLocaleBundle` in your [AppKernel.php](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/AppKernel.php)

```
//new Kunstmaan\LanguageChooserBundle\KunstmaanLanguageChooserBundle(),
//new Lunetics\LocaleBundle\LuneticsLocaleBundle(),
```

## 4) Change your config.yml

Comment out the `kunstmaan_language_chooser` configuration section in your [config.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/config.yml)

```
#kunstmaan_language_chooser:
#    autodetectlanguage: false
#    showlanguagechooser: true
#    languagechoosertemplate: CompanyYourBundle:Default:language-chooser.html.twig
#    languagechooserlocales: [nl, fr, de, en]
```

## 5) Change your bundle's service.yml

Comment out the default locale listener in your bundle's `service.yml`

```
#    companyyourbundle.default_locale_listener:
#        class: Company\YourBundle\EventListener\DefaultLocaleListener
#        tags:
#            - { name: kernel.event_listener, event: kernel.response, method: onKernelResponse }
#        arguments: [%defaultlocale%]
```

## 6) Change parameters.yml

Configure the parameters.yml file like so:

```
    requiredlocales: en
    defaultlocale: en
    multilanguage: false
```

## 7) Prevent the admin from being cached

Change

```
fos_http_cache:
    cache_control:
	rules:
	    # match admin area
	    -
		match:
		    path: ^/[^/]+/admin
		headers:
		    cache_control:
			public: false
			max_age: 0
			s_maxage: 0
		    last_modified: "-1 hour"
```

to

```
fos_http_cache:
    cache_control:
	rules:
	    # match admin area
	    -
		match:
		    path: ^/admin
		headers:
		    cache_control:
			public: false
			max_age: 0
			s_maxage: 0
		    last_modified: "-1 hour"
```


And that's it, you now have a single language website.