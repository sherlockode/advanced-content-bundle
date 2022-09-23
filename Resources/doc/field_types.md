Field Types
===========

## Simple field types 

| Name | Code | Field Raw value |
| ---- | ---- | ------- |
| Text | text | string |
| Text Area | textarea | string |

## Other field types 

| Name | Code | Field Raw value |
| ---- | ---- | ------- |
| Wysiwyg Editor | wysiwyg | string |
| Link | link | array indexed by "url", "title" |
| Relative Link | relative_link | array indexed by "url", "title" - "url" will be automatically generated depending on your http scheme and host |
| Iframe | iframe | array indexed by "href" |
| File upload | file | array indexed by "src" (file name), "url" (full url to the file resource), "title" |
| Image upload | image | array indexed by "src" (file name), "url", (full url to the image resource), "alt" |

## Using a custom FieldType

You can create your own FieldType class matching your needs.

### FieldType Class

Your class must implement `\Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface`

It can inherit `\Sherlockode\AdvancedContentBundle\FieldType\AbstractFieldType`
which already defines some interface methods for standard behavior.

You will need to define a unique code for your field in the `getCode()` method.

### FieldType Service

```yaml
# config/services.yaml
app.field_type.custom_field_type:
    class: App\FieldType\CustomFieldType
    tags:
        - { name: sherlockode_advanced_content.fieldtype }
```

### Specific Form types

There are some specific form types available to ease integration of specific use cases.

#### EntityType

You can use the `Sherlockode\AdvancedContentBundle\Form\EntityType` to integrate an Entity form in your custom field type.
The class directly extends the native EntityType from Symfony and adds a transformer to handle ID storage.

Considering a `CustomField` class with uses `CustomType` as a form, you can define your form class like this:

```php
<?php
namespace App\Form\Type;

use App\Entity\Product;
use Sherlockode\AdvancedContentBundle\Form\Type\EntityType;

class CustomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
            ])
        ;
    }
}
```

#### RepeaterType

The `RepeaterType` lets you define easily a collection of elements in a field.
The repeater will automatically and addition, removal and sorting of the elements for the end user.
The RepeaterType extends the Symfony `CollectionType` so all native options are available.

```php
<?php
namespace App\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterType;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories', RepeaterType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['attr' => ['class' => 'my-text']],
            ])
        ;
    }
}
```
