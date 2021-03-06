# Sherlockode AdvancedContentBundle

This bundle provides advanced CMS features inspired by the Wordpress ACF plugin.

Users are able to define content types as a list of fields.
These models are used by back-office contributors to create actual contents based on the previously defined structure.

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

### Twig

You may enable the bootstrap form theme in your configuration for better-looking forms in the bundle:

```yaml
# config/packages/twig.yml
twig:
    form_themes: ['bootstrap_4_layout.html.twig']
```

### Entities

`SherlockodeAdvancedContentBundle` provides 8 entity models : ContentType, Layout, Field, Content, PageType, Page, FieldGroupValue and FieldValue
To be able to use them, you need to create your own entities, see examples in the [doc](Resources/doc/entities.md).

Next, make the bundle aware of your entities by adding the following lines to your configuration:

```yaml
# config/packages/doctrine.yml
doctrine:
    orm:
        resolve_target_entities:
            SherlockodeAdvancedContentBundle\Model\ContentTypeInterface: App\Entity\ContentType
            SherlockodeAdvancedContentBundle\Model\ContentInterface: App\Entity\Content
            SherlockodeAdvancedContentBundle\Model\FieldInterface: App\Entity\Field
            SherlockodeAdvancedContentBundle\Model\FieldValueInterface: App\Entity\FieldValue
            SherlockodeAdvancedContentBundle\Model\FieldGroupValueInterface: App\Entity\FieldGroupValue
            SherlockodeAdvancedContentBundle\Model\LayoutInterface: App\Entity\Layout
            SherlockodeAdvancedContentBundle\Model\PageTypeInterface: App\Entity\PageType
            SherlockodeAdvancedContentBundle\Model\PageInterface: App\Entity\Page
            SherlockodeAdvancedContentBundle\Model\PageMetaInterface: App\Entity\PageMeta
```

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    entity_class:
        content_type: App\Entity\ContentType
        field: App\Entity\Field
        content: App\Entity\Content
        field_value: App\Entity\FieldValue
        field_group_value: App\Entity\FieldGroupValue
        layout: App\Entity\Layout
        page_type: App\Entity\PageType
        page: App\Entity\Page
        page_meta: App\Entity\PageMeta
```


### Upload configuration

If you want to use the `Image` field type, you need to configure the directory in which the images will be saved.

If not defined, all images will be saved in the system's temporary directory.

The `uri_prefix` is used to retrieve the image on display.
The resulting image URL will be the URI prefix with the uploaded file name appended.

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    upload:
        image_directory: '%kernel.project_dir%/uploads/acb_images'
        uri_prefix: /uploads/acb_images
```

### Routing

The routing is split into several files for better import rules.

* tools.xml : Routes for tooling pages, like import/export
* content.xml : Utility routes for editing contents
* content_type.xml : Utility routes for editing content types
* page.xml : basic CRUD routes for Pages (demo purpose)
* content_crud.xml : basic CRUD routes for Content and ContentTypes (demo purpose)
* all.xml : includes all of the above
* base.xml : includes tools.xml, content.xml and content_type.xml

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


## License

This bundle is under the MIT license. Check the details in the [dedicated file](LICENSE)
