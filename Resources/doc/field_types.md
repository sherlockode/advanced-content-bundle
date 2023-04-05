# Field Types

----

## Simple field types 

| Name      | Code     | Field Raw value |
|-----------|----------|-----------------|
| Text      | text     | string          |
| Text Area | textarea | string          |

## Other field types 

| Name           | Code           | Field Raw value                                                                                                                                   |
|----------------|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| Wysiwyg Editor | wysiwyg        | string                                                                                                                                            |
| Link           | link           | "url", "title"                                                                                                                                    |
| Relative Link  | relative_link  | "url", "title" - "url" will be automatically generated depending on your http scheme and host                                                     |
| Iframe         | iframe         | "href"                                                                                                                                            |
| File upload    | file           | "src" (file name), "url" (full url to the file resource), "title"                                                                                 |
| Image upload   | image          | "src" (file name), "url", (full url to the image resource), "alt"                                                                                 |
| Image carousel | image_carousel | "images" array. Each image is indexed with "src", "url" and "alt". Two booleans variables are also available: displayPagination and displayArrows |
| Separator      | separator      | none                                                                                                                                              |
| Title          | title          | "text" for the content of the title. "Level" (1 to 6) for the heading type.                                                                       |
| Video          | video          | "url" for the video url. Then options for the player: "muted", "autoplay", "loop", "controls", "height", "width"                                  |
| Content        | content        | "entity" contains the Content object (if found, null otherwise). "content" contains the slug of the content.                                      |                                                                                                      |


## Specific Form types

There are some specific form types available to ease integration of specific use cases.

### EntityType

----

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

### RepeaterType

----

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
