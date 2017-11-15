# KunstmaanMenuBundle

Some websites have multiple menus, for example: a top menu, a secondary top menu, a footer menu, etc... The default
KunstmaanBundlesCMS menu system can only handle 1 menu (or multiple menus that have the same structure and menu items). 
With KunstmaanMenuBundle you can define multiple menus that each can be configured via the administrator interface.

## Usage

### Define the different menus

First you need to define all the different website menus in your `config.yml` file.

```yml
kunstmaan_menu:
    menus: [footer, secondary_top]
```

### Manage the menus via the administrator interface

Then you'll see the "Menus" menu item in the "Modules" top menu. You can manage the menu items for each site language.
When you add a new menu item, you can choose between a "page link" which links to one of the website pages or a 
"url link" which can link to any webpage on the internet.

### Render the menus

The last step is calling the `get_menu` twig function to render the menus on the correct location in the html.

```
{{ get_menu('footer', app.request.locale, {'rootOpen': '<ul class="footer-nav__list">', 'childOpen': '<li class="footer-nav__list-item">'}) }}
```

It is possible to override some options to customize the html that will be generated in the twig extension.

* rootOpen: default `<ul>`
* rootClose: default `</ul>`
* childOpen: default `<li>`
* childClose: default `</li>`
