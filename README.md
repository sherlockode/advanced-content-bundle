# Sherlockode AdvancedContentBundle

----

[ ![](https://img.shields.io/packagist/l/sherlockode/advanced-content-bundle)](https://packagist.org/packages/sherlockode/advanced-content-bundle "License")
[ ![](https://img.shields.io/packagist/v/sherlockode/advanced-content-bundle)](https://packagist.org/packages/sherlockode/advanced-content-bundle "Version")
[ ![](https://poser.pugx.org/sherlockode/advanced-content-bundle/downloads)](https://packagist.org/packages/sherlockode/advanced-content-bundle "Total Downloads")
[ ![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://www.sherlockode.fr/contactez-nous/?utm_source=github&utm_medium=referral&utm_campaign=plugins_acb)

![image](https://user-images.githubusercontent.com/22291441/230099691-0fad8407-9883-4f0c-bdbd-9d6a8245a8db.png)

## Table of Content

---- 

* [Overview](#overview)
* [Installation](#installation)
* [Terminology](#terminology)
* [Configuration](#configuration)
    * [Assets](#assets)
    * [Twig](#twig)
    * [Entities](#entities)
    * [Upload](#upload-configuration)
    * [Routing](#routing)
* [Advanced documentation](#advanced-documentation)
* [Dependencies](#dependencies)
* [License](#license)
* [Contact](#contact)

# Overview 

---- 

This bundle provides advanced CMS features for end user contribution.

Users can build their website pages quickly and effortlessly thanks to our intuitive interface.
Several standard elements are included, such as text block, heading, image, image carousel, video player, ...
Responsive layouts can be shaped as needed with row and column elements.
Drafts are saved automatically and it's easy to rollback to a previous version.
Custom elements can be added simply with a few lines of code.

# Installation

----

## Get the bundle using composer

The best way to install this bundle is to rely on [Composer](https://getcomposer.org/):

```bash
$ composer require sherlockode/advanced-content-bundle
```

## Enable the bundle

Register the bundle in your application's kernel:

```php
// config/bundles.php
<?php

return [
    /* ... */
    Sherlockode\AdvancedContentBundle\SherlockodeAdvancedContentBundle::class => ['all' => true],
];

```

# Terminology

----

## Entities

----

A Page is linked to a PageMeta and to a Content.\
It can optionally be linked to a PageType too.

Content can be used independently as well.\
For example, if several Pages include the same layer, you can create a single Content with this layer and then include the Content within the Pages.\
As a result, if the layer has to change, you only have to change a single Content instead of all the Pages.

You can also create a Content to make some parts of your website dynamic.\
For example, you can create a Content which includes your footer links. This way you don't have to change your template everytime the footer has to be updated.

Pages, PageMetas and Contents are all versionable, hence the PageVersion, PageMetaVersion and ContentVersion entities.

Finally, if you enabled the scope management, you will be able to associate each Page and Content to one or multiple Scopes.

## Elements

----

Content data is an array of Elements.\
Elements can either be a Layout or a FieldType.\
Layouts include rows and columns.\
FieldTypes include the field types we defined (text, image, video, ...) and you custom field types.

# Configuration

----

## Assets

----

jQuery and jQuery UI are mandatory and should be required in `package.json` in order for the assets build to work.

Please note that Font Awesome is optional, but natively used for icons display on contribution pages.
To use it you should require `@fortawesome/fontawesome-free` in `package.json` (or use another install method of your choice).

```json
{
  "dependencies": {
    "@fortawesome/fontawesome-free": "^6.1.2",
    "jquery": "^3.5.0",
    "jquery-ui": "1.12.1"
  }
}
```

You should import the provided assets in your main asset file to integrate them in your asset build process.


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

## Twig

----

The bundle automatically uses Bootstrap 5 (or Bootstrap 4 for Symfony < 5.3) as the base form theme for all forms.

If you just want to use a different form theme,
you can override the bundle's base form theme file `Form/base_theme.html.twig`

```
{# templates/bundles/SherlockodeAdvancedContentBundle/Form/base_theme.html.twig #}

{% extends 'form_div_layout.html.twig' %}
```

## Entities

----

`SherlockodeAdvancedContentBundle` provides 8 entity models.
To be able to use them, you need to create your own entities, see examples in the [doc](Resources/doc/entities.md),
and fill the corresponding configuration :

```yaml
# config/packages/sherlockode_advanced_content.yaml
sherlockode_advanced_content:
    entity_class:
        content: App\Entity\Content
        content_version: App\Entity\ContentVersion
        page: App\Entity\Page
        page_meta: App\Entity\PageMeta
        page_meta_version: App\Entity\PageMetaVersion
        page_type: App\Entity\PageType
        page_version: App\Entity\PageVersion
        scope: App\Entity\Scope
```


## Upload configuration

----

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

## Routing

----

The routing is split into several files for better import rules.

* content.xml : Utility routes for editing contents
* content_crud.xml : basic CRUD routes for Content (demo purpose)
* page.xml : Utility routes for editing pages
* page_crud.xml : basic CRUD routes for Pages (demo purpose)
* scope_crud.xml : basic CRUD routes for Scopes (demo purpose)
* tools.xml : Routes for tooling pages, like import/export, Page Type CRUD
* all.xml : includes all the above
* base.xml : includes tools.xml, content.xml and page.xml

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

# Advanced Documentation

----

- [Entities](Resources/doc/entities.md)
- [Elements](Resources/doc/elements.md)
- [Tools](Resources/doc/tools.md)
- [Import](Resources/doc/import.md)
- [Export](Resources/doc/export.md)
- [Usage](Resources/doc/usage.md)
- [Data migration](Resources/doc/data_migration.md)


# Dependencies

----

This bundle is compatible with webpack.
jQuery library is mandatory.
jQuery ui sortable is advised but not required.
Some field types, such as the image carousel, use Bootstrap 5 for their display. 


# License

----

This bundle is under the MIT license. Check the details in the [dedicated file](LICENSE)


# Contact

----

If you want to contact us, the best way is to fill the form on [our website](https://www.sherlockode.fr/contactez-nous/?utm_source=github&utm_medium=referral&utm_campaign=plugins_acb) or send us an e-mail to contact@sherlockode.fr with your question(s). We guarantee that we answer as soon as we can!
