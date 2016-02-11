KunstmaanLanguageChooserBundle
==============================

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanLanguageChooserBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanLanguageChooserBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/languagechooser-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/languagechooser-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/languagechooser-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/languagechooser-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanLanguageChooserBundle)](https://github.com/igrigorik/ga-beacon)

Handles autodetection of the language of the end user or shows a splash page with a language choice.


Installation instructions
-------------------------

Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/languagechooser-bundle dev-master
```

Add the KunstmaanLanguageChooserBundle and the LuneticsLocaleBundle to your AppKernel.php file:

``` php
new Kunstmaan\LanguageChooserBundle\KunstmaanLanguageChooserBundle(),
new Lunetics\LocaleBundle\LuneticsLocaleBundle(),
```

Remark: The KunstmaanLanguageChooserBundle should be loaded __BEFORE__ the LuneticsLocaleBundle

Add the KunstmaanLanguageChooserBundle to your routing.yml:

``` yaml
# KunstmaanLanguageChooserBundle
_languagechooser:
    resource: .
```

Overwrite the KunstmaanLanguageChooserBundle config to your needs in config.yml:

``` php
kunstmaan_language_chooser:
    autodetectlanguage: false
    showlanguagechooser: true
    languagechoosertemplate: CompanyYourBundle:Default:language-chooser.html.twig
    languagechooserlocales: [en, nl, fr]
```

Usage
-----

When the user arrives on the root page of your website (eg. http://domain.com/) he:
- is automatically redirect to his language of choice (eg. http://domain.com/en) when the `autodetectlanguage` is set to `true`
- sees a splash page where he can select his language manually when the `showlanguagechooser` is set to `true`

The template path of the splash page can we set via the `languagechoosertemplate` configuration parameter.

In all your Twig templates, there is a global variable available with an array of the available languages: `languagechooser_languages`.
This can be usefull on your custom splash page, or when you want to show the language choice on other pages.

```
<ul>
    {% for lang in languagechooser_languages %}
        <li>{{ lang }}</li>
    {% endfor %}
</ul>
```
