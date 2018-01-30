# UPGRADE FROM 4.0 to 4.1

## RedirectBundle

Added note to the RedirectBundle:

https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1490

Please be sure to execute database migrations after upgrading.

## KunstmaanCacheBundle

We added a new caching bundle.

https://github.com/Kunstmaan/KunstmaanBundlesCMS/tree/master/docs/05-14-using-the-cache-bundle.md

This bundle allows you to do stuff with caching. For now it's a starting bundle with the possibility to ban stuff from varnish.

## Kunstmaan translations with crowdin

We have chosen to make all bundle translations available at the translation platform "Crowdin". 

https://crowdin.com/project/kunstmaanbundlescms 

If you work on a patch for KunstmaanBundlesCMS, adding new phrases is really simple and works like with any Symfony application.
You add the new translation keys to the YAML formatted files in bundle's Resources/translations directory. 

When new phrases are added in English, you have to create an account on translatebundles.kunstmaan.be. 
The UI is very intuitive and all you need is to navigate to correct language and start translating the files!

Currently, the synchronization needs to be performed manually.
Kunstmaan team will use the sync commands and add new translations to the GitHub repository.

http://translatebundles.kunstmaan.be/
