Manage translations
====================

For those who want to make a multi-language website and edit translations easily, life became much easier.
We've created a Translator bundle to edit your translations from withing our backend.

Features
---------------------------------------
* Import bundle specific translations from any type of translation file
* Import global translations form any type of translation file
* Live edit existing translations from the backend
* Add new translations from the backend
* Native translation caching
* Notices when cached translations aren't up to date
* Clear translation cache from backend or the cli
* Check translation cache from the cli
* Create doctrine migration of all changed/added translations
* Preview all managed translations from each request in the Symfony2 profiler

1) Import translations
---------------------------------------
You can import translations from the backend, go to Settings -> Translations.
Here you will see all translations managed from the TranslatorBundle.
Now you have two ways to import new or existing translations. A normal 'Import' will import all new translations from the current bundle. 'Import forced' will also import all new translations form the current bundle __AND__ overwrite existing translations. So be careful with the forced import functionality.

We've also included the ability to import translations from the __cli__.
This method is much more flexible and has more options.

- A __normal__ import (same import as in the backend)

```
app/console kuma:translator:import --forced
```

- A __forced__ import

```
app/console kuma:translator:import
```
Note: both normal and forced import will only import translations from the current bundle.

- Import translations from specific bundle

```
app/console kuma:translator:import --bundle=superCoolNewApplicationBundle
```

- Import translations only for specified locales

```
app/console kuma:translator:import --locales=nl,fr,de
```
- Import translations from the global Resources (```app/Resoureces/translations```)

```
app/console kuma:translator:import --globals
```

2) Translation caching and clearing
---
Translations are stored in database, but we're using the native translator caching from Symfony2 when running your application with ```debug = false```.

This means that the following kernel configuration will __always__ look for translations in the database:

```
$kernel = new AppKernel('dev', true);
```

And this kernel configuration will once fetch all translations and cache them into ```app/cache/translations```

```
$kernel = new AppKernel('prod', false);
```

So when you've ```debug mode``` off and changes are done in the backend, you'll see a notice. This notice will tell you that not all translations are up to date (in the cache). You can either click ```Refresh live``` or refresh the cache from the cli:

```
app/console kuma:translator:cache --flush
```

You can also request the current cache status:

```
app/console kuma:translator:cache --status
```

3) Configuration
---

The ```kunstmaan_translator``` namespace has a few configuration options, a quick summary with their defaults:

```
kunstmaan_translator:
	enabled: 			true
	default_bundle: 	kunstmaantranslatorbundle
	cache_dir: 			%kernel.cache_dir%/translations
	managed_locales:	[]
	file_formats:		['yml', 'xliff']

```

* ```enabled``` : Enabled or disable the KunstmaanTranslatorBundle
* ```default_bundle``` : Default bundle used for the import from within the backend (not case-sensitive)
* ```cache_dir```: Cached translations dir
* ```managed_locales```: Which locale translation files should be imported
* ```file_formats```: Which type of translation files should be imported

We advise you to overwrite the ```default_bundle``` configuration, as you will otherwise only include the KunstmaanTranslatorBundle translations.