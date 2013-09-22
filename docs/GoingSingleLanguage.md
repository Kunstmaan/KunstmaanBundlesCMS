Going single language
=====================

The Standard Edition installs a multilanguage site. This is important for us because Belgium is a multilingual country, but it's clear that there are a lot of cases where it is not needed. While the CMS will always retain it multilingual capabilities, we want to remove the language from the URL. 

## 1) Change the routing.yml

Switch out the default [routing.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/routing.yml) with [routing.singlelang.yml](https://github.com/Kunstmaan/KunstmaanBundlesStandardEdition/blob/master/app/config/routing.singlelang.yml)

```
mv app/config/routing.yml app/config/routing.multilang.yml 
mv app/config/routing.singlelang.yml app/config/routing.yml 
```
