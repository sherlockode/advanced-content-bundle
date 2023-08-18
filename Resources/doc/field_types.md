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

### AcbFileType and ImageType restrictions

For these form types, you have the possibility to restrict the type of file uploaded.
By default, bundle have 7 groups :

- image
  - image/*
  
- pdf
  - application/pdf

- executable
  - application/vnd.microsoft.portable-executable

- archive
  - application/zip
  - application/x-rar-compressed
  - application/x-tar
  - application/x-7z-compressed

- text_file
  - text/plain
  - application/msword
  - application/vnd.openxmlformats-officedocument.wordprocessingml.document
  - application/vnd.ms-word.document.macroEnabled.12
  - application/vnd.ms-powerpoint
  - application/vnd.openxmlformats-officedocument.presentationml.presentation
  - application/vnd.openxmlformats-officedocument.wordprocessingml.template
  - application/xml

- spreadsheet
  - text/csv
  - application/rtf
  - application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
  - application/vnd.ms-excel
  - application/msexcel
  - application/vnd.ms-works
  - application/x-msworks
  - application/vnd.ms-excel
  - application/vnd.ms-excel.sheet.macroEnabled.12
  - application/vnd.ms-excel.sheet.binary.macroEnabled.12
  - application/vnd.ms-excel.template.macroEnabled.12
  
- multimedia
  - video/mp4
  - video/mpeg
  - video/ogg
  - video/3gpp
  - video/MP2T
  - audio/mpeg
  - audio/mp4
  - audio/vnd.wa
  - audio/ogg

_For ImageType, only image group restriction is used._

Need to modify something ? Allow only jpeg and webp image file:  

```yaml
#sherlockode_advanced_content.yaml
sherlockode_advanced_content:
    mime_type_group:
        image:
            - image/jpeg
            - image/webp
```
This declaration overrides the existing declaration, if you only want to add a mime type in a group, you need to add the default mime types.
