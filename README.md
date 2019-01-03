# Sherlockode AdvancedContentBundle

This bundle provides advanced CMS features inspired by the wordpress ACF plugin.

Users are able to define content types as a list of fields.
These models are used by contributors to create actual contents based on the previously defined structure.

Installation
------------

### Get the bundle using composer

Add Sherlockode/AdvancedContentBundle by running this command from the terminal at the root of your Symfony project:

```bash
composer require sherlockode/advanced-content-bundle
```

### Enable the bundle

Register the bundle in your application's kernel class:

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Sherlockode\AdvancedContentBundle\SherlockodeAdvancedContentBundle(),
            // ...
        ];
    }
}
```

Configuration
-------------

### Create your entities

AdvancedContentBundle provides 6 entity models : ContentType, Layout, Field, Content, FieldGroupValue and FieldValue
To be able to use them, you need to create your own entities : 

```php
<?php
// src/AppBundle/Entity/ContentType.php

namespace AppBundle\Entity;

use Sherlockode\AdvancedContentBundle\Model\ContentType as BaseContentType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type")
 */
class ContentType extends BaseContentType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Field", mappedBy="contentType", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Content", mappedBy="contentType", cascade={"persist", "remove"})
     */
    protected $contents;
}
```

```php
<?php
// src/AppBundle/Entity/Field.php

namespace AppBundle\Entity;

use Sherlockode\AdvancedContentBundle\Model\Field as BaseField;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="field")
 */
class Field extends BaseField
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ContentType", inversedBy="fields")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FieldValue", mappedBy="field", cascade={"persist", "remove"})
     */
    protected $fieldValues;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Layout", inversedBy="children")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $layout;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Layout", mappedBy="parent", cascade={"persist", "remove"})
     */
    protected $children;
}
```

```php
<?php
// src/AppBundle/Entity/Content.php

namespace AppBundle\Entity;

use Sherlockode\AdvancedContentBundle\Model\Content as BaseContent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content")
 */
class Content extends BaseContent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ContentType", inversedBy="contents")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FieldValue", mappedBy="content", cascade={"persist", "remove"})
     */
    protected $fieldValues;
}
```

```php
<?php
// src/AppBundle/Entity/FieldValue.php

namespace AppBundle\Entity;

use Sherlockode\AdvancedContentBundle\Model\FieldValue as BaseFieldValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="field_value")
 */
class FieldValue extends BaseFieldValue
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Content
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;

    /**
     * @var Field
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;

    /**
     * @var FieldGroupValue
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FieldGroupValue", inversedBy="children")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $group;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FieldGroupValue", mappedBy="parent", cascade={"persist", "remove"})
     */
    protected $children;
}
```

```php
<?php
// src/AppBundle/Entity/FieldGroupValue.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValue as BaseFieldGroupValue;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="field_group_value")
 */
class FieldGroupValue extends BaseFieldGroupValue
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var FieldValueInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FieldValue", inversedBy="children")
     */
    protected $parent;

    /**
     * @var FieldValueInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FieldValue", mappedBy="group", cascade={"persist", "remove"})
     */
    protected $children;

    /**
     * @var LayoutInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Layout")
     */
    protected $layout;
}
```

```php
<?php
// src/AppBundle/Entity/Layout.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\Layout as BaseLayout;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="layout")
 */
class Layout extends BaseLayout
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * @var FieldInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="children")
     */
    protected $parent;

    /**
     * @var FieldInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Field", mappedBy="layout", cascade={"persist", "remove"})
     */
    protected $children;
}
```

### Entity Mapping

Make the bundle aware of your entities by adding a few lines to your configuration:

```yaml
# app/config/config.yml
doctrine:
    orm:
        resolve_target_entities:
            SherlockodeAdvancedContentBundle\Model\ContentTypeInterface: AppBundle\Entity\ContentType
            SherlockodeAdvancedContentBundle\Model\ContentInterface: AppBundle\Entity\Content
            SherlockodeAdvancedContentBundle\Model\FieldInterface: AppBundle\Entity\Field
            SherlockodeAdvancedContentBundle\Model\FieldValueInterface: AppBundle\Entity\FieldValue
            SherlockodeAdvancedContentBundle\Model\FieldGroupValueInterface: AppBundle\Entity\FieldGroupValue
            SherlockodeAdvancedContentBundle\Model\LayoutInterface: AppBundle\Entity\Layout

sherlockode_advanced_content:
    entity_class:
        content_type: AppBundle\Entity\ContentType
        field: AppBundle\Entity\Field
        content: AppBundle\Entity\Content
        field_value: AppBundle\Entity\FieldValue
        field_group_value: AppBundle\Entity\FieldGroupValue
        layout: AppBundle\Entity\Layout
```


### Upload configuration

If you want to use the Image field type, you need to configure the directory in which the images will be saved.

If not defined, images will be saved in the system's temporary directory.

THe uri_prefix is used to retrieve the image on display.
The resulting image URL will be the URL prefix with the uploaded file name appended.

```yaml
# app/config/config.yml
sherlockode_advanced_content:
    upload:
        image_directory: '%kernel.project_dir%/uploads/acb_images'
        uri_prefix: /uploads/acb_images
```


### Routing

```yaml
# app/config/routing.yml
sherlockode_advanced_content:
    resource: '@SherlockodeAdvancedContentBundle/Resources/config/routing.yml'
```


Usage
-----

The bundle provides a twig function that will render the html of a field for a given content : 

```twig
{{ acb_field(content, slug) }}
```

Note that each FieldType has a `render()` method that will output the html for a given field.


For fields with hierarchy, like the Repeater, you will need to build a loop that will browse each
group of FieldValue and allow you to render each field of this group.
```twig
{% for group in acb_groups(yourContent, 'my-repeater-field') %}
    {{ acb_group_field(group, 'first-field') }}
    {{ acb_group_field(group, 'other-field') }}
{% endfor %}
```
