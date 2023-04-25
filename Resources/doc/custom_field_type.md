# Using a custom FieldType

----

You can create your own FieldType class matching your needs.\
For our example, we will add a new field type allowing you to create a collection of reviews.

## FieldType Class

Your class must implement `\Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface`

It can inherit `\Sherlockode\AdvancedContentBundle\FieldType\AbstractFieldType`
which already defines some interface methods for standard behavior.

You will need to define a unique code for your field in the `getCode()` method.

Example

```php
<?php
// src/FieldType/Reviews.php

namespace App\FieldType;

use App\Form\Type\ReviewsType;
use Sherlockode\AdvancedContentBundle\FieldType\AbstractFieldType;

class Reviews extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getCode()
    {
        return 'reviews';
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ReviewsType::class;
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'acme';
    }

    /**
     * @return string
     */
    public function getPreviewTemplate()
    {
        return 'templates/admin/Acb/reviews.html.twig';
    }

    /**
     * @return string
     */
    public function getFrontTemplate()
    {
        return 'templates/Acb/reviews.html.twig';
    }
}

```

To translate the FieldType label, create your own AdvancedContentBundle.\[lang\].yaml

```yaml
field_type:
    reviews:
        label: Reviews
```

## FieldType Service

If your services are not autoconfigured, you should add these lines to your services.yaml:

```yaml
# config/services.yaml
app.field_type.custom_field_type:
    class: App\FieldType\CustomFieldType
    tags:
        - { name: sherlockode_advanced_content.fieldtype }
```

## FieldType Form

```php
<?php
// src/Form/Type/ReviewsType.php

namespace App\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReviewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reviews', RepeaterType::class, [
                'entry_type' => ReviewType::class,
            ])
        ;
    }
}

```

```php
<?php
// src/Form/Type/ReviewType.php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
        ;
    }
}

```

In the admin, you can then add some reviews. Your custom form type is automatically inserted into the panel, as well as the extra configuration tabs.

![image](https://user-images.githubusercontent.com/22291441/230105197-a8b63d0c-5154-4b83-83ae-24052b6c26b1.png)

## Templates

Thanks to the preview template, you can anticipate how your content will look like on the front

![image](https://user-images.githubusercontent.com/22291441/230106951-84d6307d-e39f-4587-90a7-5a8911f1da08.png)

Preview template
```twig
{# templates/admin/Acb/reviews.html.twig #}

{% if reviews|length > 0 %}
    <div class="row wrapper-review">
        {% for review in reviews %}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        {% if review.title is not null %}
                            <div class="card-title">{{ review.title }}</div>
                        {% endif %}
                        {% if review.description is not null %}
                            {{ review.description|nl2br }}
                        {% endif %}
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% endif %}

```

For the front template, you should also include the extra configuration information.\
Twig function "acb_get_element_attributes" will give you all necessary data to customize the display of your field.
This function will return an array indexed with "id", "classes" and "style" (calculated from the design configuration tab).

```twig
{# templates/Acb/reviews.html.twig #}

{% if reviews|length > 0 %}
    {% set attributes = acb_get_element_attributes(extra|default([]), 'block') %}
    {% set style = attributes.style|default('') %}

    <div class="row wrapper-review {{ attributes.classes }}" 
         {% if attributes.id %}id="{{ attributes.id }}"{% endif %}
         {% if style|trim is not empty %}style="{{ style }}"{% endif %}
    >
        {% for review in reviews %}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        {% if review.title is not null %}
                            <div class="card-title">{{ review.title }}</div>
                        {% endif %}
                        {% if review.description is not null %}
                            {{ review.description|nl2br }}
                        {% endif %}
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% endif %}

```
