# Sherlockode AdvancedContentBundle

This bundle provides advanced CMS features for end user contribution.

Users are able to create pages with standard and custom building blocks.
These blocks are used by back-office contributors to create actual contents without coding after the setup is done.

[![Total Downloads](https://poser.pugx.org/sherlockode/advanced-content-bundle/downloads)](https://packagist.org/packages/sherlockode/advanced-content-bundle)
[![Latest Stable Version](https://poser.pugx.org/sherlockode/advanced-content-bundle/v/stable)](https://packagist.org/packages/sherlockode/advanced-content-bundle)

Installation
------------

### Get the bundle using composer

The best way to install this bundle is to rely on [Composer](https://getcomposer.org/):

```bash
$ composer require sherlockode/advanced-content-bundle
```

### Enable the bundle

Register the bundle in your application's kernel:

```php
// config/bundles.php
<?php

return [
    /* ... */
    Sherlockode\AdvancedContentBundle\SherlockodeAdvancedContentBundle::class => ['all' => true],
];

```

Configuration
-------------

### Assets

You may import the provided assets in your main asset file to integrate them in your asset build process.

Please note that Font Awesome is optional, but natively used for icons display on contribution pages.
To use it you should require `@fortawesome/fontawesome-free` in `package.json` (or use another install method of your choice).

```js
// assets/js/app.js
import '../../vendor/sherlockode/advanced-content-bundle/Resources/public/css/index.scss';
import '../../vendor/sherlockode/advanced-content-bundle/Resources/js/index.js';

// font awesome (optional)
import '@fortawesome/fontawesome-free/css/fontawesome.css';
import '@fortawesome/fontawesome-free/css/solid.css';
```

You can use the provided `layout.html.twig` or build your own depending on your needs.
The `symfony/webpack-encore-bundle` is required in order to use this layout with the Webpack Encore Twig functions.

```
composer require symfony/webpack-encore-bundle
```

```html
{# templates/layout.html.twig #}

{# ... #}
{{ encore_entry_link_tags('app') }}
{# ... #}
{{ encore_entry_script_tags('app') }}
```

### Twig

You may enable the bootstrap form theme in your configuration for better-looking forms in the bundle:

```yaml
# config/packages/twig.yml
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
```

Take care that we use the last defined form theme as the base theme for all forms.

Alternatively, if you run into theme issues or if you just want to use a different form theme,
you can override the bundle's base form theme file `Form/base_theme.html.twig`

```
{# templates/bundles/SherlockodeAdvancedContentBundle/Form/base_theme.html.twig #}

{% extends 'bootstrap_5_layout.html.twig' %}
```

### Entities

`SherlockodeAdvancedContentBundle` provides 5 entity models : Content, FieldValue, PageType, Page, PageMeta.
To be able to use them, you need to create your own entities, see examples in the [doc](Resources/doc/entities.md).

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    entity_class:
        content: App\Entity\Content
        field_value: App\Entity\FieldValue
        page_type: App\Entity\PageType
        page: App\Entity\Page
        page_meta: App\Entity\PageMeta
```


### Upload configuration

If you want to use the `Image` or `File` field type, you need to configure the directory in which the images will be saved.

If not defined, all images will be saved in the system's temporary directory.

The `uri_prefix` is used to retrieve the image on display.
The resulting image URL will be the URI prefix with the uploaded file name appended.

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    upload:
        image_directory: '%kernel.project_dir%/public/uploads/acb_images'
        uri_prefix: /uploads/acb_images
```

### Routing

The routing is split into several files for better import rules.

* tools.xml : Routes for tooling pages, like import/export
* content.xml : Utility routes for editing contents
* page_meta.xml : Utility routes for editing pages
* page.xml : basic CRUD routes for Pages (demo purpose)
* content_crud.xml : basic CRUD routes for Content (demo purpose)
* all.xml : includes all the above
* base.xml : includes tools.xml, content.xml and page_meta.xml

The base.xml file is sufficient if you plan to manage all your CRUD operations in custom controllers
(like if you use an external admin system).

```yaml
# config/routes.yaml
sherlockode_advanced_content:
    prefix: '/acb'
    resource: '@SherlockodeAdvancedContentBundle/Resources/config/routing/base.xml'
    # or include all routes directly
    # resource: '@SherlockodeAdvancedContentBundle/Resources/config/routing/all.xml'
```

## Advanced Documentation

- [Entities](Resources/doc/entities.md)
- [Field Types](Resources/doc/field_types.md)
- [Tools](Resources/doc/tools.md)
- [Import](Resources/doc/import.md)
- [Export](Resources/doc/export.md)
- [Usage](Resources/doc/usage.md)

## Dependencies

This bundle is compatible with webpack.
jQuery library is mandatory.
jQuery ui sortable is advised but not required.

## License

This bundle is under the MIT license. Check the details in the [dedicated file](LICENSE)
