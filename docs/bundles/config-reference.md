# Configuration Options

Ever wondered what configuration options you have available to you in yaml config files? In this section,
all the available configurations are broken down for each bundle (e.g. AdminBundle, NodeBundle) that defines each
possible section of your Kunstmaan cms configuration.

## Adminbundle

### Full Default Configuration

```yaml
kunstmaan_admin:
    admin_password:       ~
    dashboard_route:      ~
    admin_prefix:         admin
    admin_locales:
        - en
    session_security:
        ip_check:             false
        user_agent_check:     false
    admin_exception_excludes: []
    default_admin_locale: en
    enable_console_exception_listener: true
    enable_toolbar_helper: false
    provider_keys:        []
    menu_items:
        # Prototype
        route:
            route:                ~ # Required
            label:                ~ # Required
            role:                 null
            params:               []
            parent:               KunstmaanAdminBundle_modules
    google_signin:
        enabled:              false
        client_id:            null
        client_secret:        null
        hosted_domains:
            # Prototype
            -
                domain_name:          ~ # Required
                access_levels:        [] # Required
    password_restrictions:
        min_digits:           null
        min_uppercase:        null
        min_special_characters: null
        min_length:           null
        max_length:           null
```

## AdminListBundle

### Full Default Configuration

```yaml
kunstmaan_k_admin_list:
    lock:
        enabled:              false
        check_interval:       15
        threshold:            35
```

## ArticleBundle

This bundle has no config options.

## DashboardBundle

This bundle has no config options.

## FormBundle

This bundle has no config options.

## MediaBundle

### Full Default Configuration

```yaml
kunstmaan_media:
    soundcloud_api_key:   YOUR_CLIENT_ID
    remote_video:
        vimeo:                true
        youtube:              true
        dailymotion:          true
    enable_pdf_preview:   false
    blacklisted_extensions:

        # Defaults:
        - php
        - htaccess
    whitelisted_extensions: []
```

## MediaPagePartBundle

This bundle has no config options.

## NodeBundle

### Full Default Configuration

```yaml
kunstmaan_node:
    pages:

        # Prototype
        -
            name:                 ~ # Required
            search_type:          ~
            structure_node:       ~
            indexable:            ~
            icon:                 null
            hidden_from_tree:     ~
            is_homepage:          ~
            allowed_children:

                # Prototype
                -
                    class:                ~ # Required
                    name:                 ~
    publish_later_stepping: '15'
    unpublish_later_stepping: '15'
    show_add_homepage:    true
    lock:
        enabled:              false
        check_interval:       15
        threshold:            35
    enable_permissions: true # Enable/disable permission checking on nodes (frontend and admin view)
```

## NodeSearchBundle

### Full Default Configuration

```yaml
kunstmaan_node_search:
    enable_update_listener: true
    use_match_query_for_title: false
    mapping:

        # Prototype
        name:
            type:                 ~
            index:                ~
            include_in_all:       ~
            store:                ~
            boost:                ~
            null_value:           ~
            analyzer:             ~
            search_analyzer:      ~
            index_analyzer:       ~
            copy_to:              ~
            term_vector:          ~
    contexts:             []
```

## PagePartBundle

### Full Default Configuration

```yaml
kunstmaan_page_part:
    extended_pagepart_chooser: false
    pageparts:

        # Prototype
        -
            name:                 ~ # Required
            context:              ~ # Required
            extends:              ~
            widget_template:      ~
            types:

                # Prototype
                -
                    name:                 ~ # Required
                    class:                ~ # Required
                    preview:              ~
                    pagelimit:            ~
    pagetemplates:

        # Prototype
        -
            template:             ~ # Required
            name:                 ~ # Required
            rows:

                # Prototype
                -
                    regions:

                        # Prototype
                        -
                            name:                 ~
                            span:                 12
                            template:             ~
                            rows:                 ~
```

## RedirectBundle

This bundle has no config options.

## SearchBundle

### Full Default Configuration

```yaml
kunstmaan_search:
    analyzer_languages:

        # Prototype
        name:
            analyzer:             ~
```

## SeoBundle

This bundle has no config options.

## SitemapBundle

This bundle has no config options.

## TranslatorBundle

### Full Default Configuration

```yaml
kuma_translator:
    enabled:              true
    default_bundle:       own
    bundles:              []
    cache_dir:            '%kernel.cache_dir%/translations'
    debug:                null
    managed_locales:      []
    file_formats:

        # Defaults:
        - yml
        - xliff
    storage_engine:
        type:                 orm
```

## UserManagementBundle

This bundle has no config options.


## UtilitiesBundle

This bundle has no config options.
