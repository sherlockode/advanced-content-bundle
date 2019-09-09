Usage
======

## Controller

### Page

To display content linked to a given page, you can have the following controller: 

```php
<?php
// App/Controller/PageController
namespace App\Controller;

use App\Entity\Page;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/{slug}", name="page_view") 
     * 
     * @param Page $page
     *
     * @return Response
     */
    public function indexAction(Page $page)
    {
        if ($page->getStatus() !== PageInterface::STATUS_PUBLISHED || !$page->getContent() instanceof ContentInterface) {
            throw $this->createNotFoundException();
        }

        return $this->render('content.html.twig', [
            'content' => $page->getContent(),
        ]);
    }
}
```

### Standalone Content

If your content is not linked to any page (for example, your homepage), you can retrieve it by using its content type :
 
 ```php
 <?php
 // App/Controller/DefaultController
 namespace App\Controller;
 
 use App\Entity\ContentType;
 use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Routing\Annotation\Route;
 
 class DefaultController extends AbstractController
 {
     /**
      * @Route("/", name="homepage") 
      * 
      * @return Response
      */
     public function indexAction()
     {
         $contentType = $this->getDoctrine()->getRepository(ContentType::class)->findOneBy(['name' => 'HomePage']);
         $content = $contentType->getFirstContent();
         
         if (!$content instanceof ContentInterface) {
             throw $this->createNotFoundException();
         }
         
         return $this->render('content.html.twig', [
             'content' => $content,
         ]);
     }
 }
 ```

## Twig

The bundle provides a twig function that will returns an array containing all field values for a given content. 

```twig
{% set fields = acb_fields(page.content) %}
```

To ease the process of the field values, they are indexed by their slug.
If the Field is a flexible or a repeater, its children will be included.


```twig
{#templates/content.html.twig#}

{% set fields = acb_fields(content) %}

{#regular field#}
<h1>{{ fields['custom-text-field']['raw'] }}</h1>

{#flexible field#}
{% for group in fields['custom-flexible-field'].children %}
    {% set sectionName = group.name %}
    {% if sectionName == 'Custom Layout 1' %}
        {{ _self.render_section_layout_1(group.children) }}
    {% elseif sectionName == 'Custom Layout 2' %}
        {{ _self.render_section_layout_2(group.children) }}
    {% endif %}
{% endfor %}

{% macro render_section_layout_1(groupChildren) %}
    <section>
        <h1>{{ groupChildren['custom-layout-1-title']['raw'] }}</h1>
    </section>
{% endmacro %}

{% macro render_section_layout_2(groupChildren) %}
    <section>
        <h1>{{ groupChildren['custom-layout-2-title']['raw'] }}</h1>
        
        {#repeater field#}
        <ul>
            {% for repeaterGroup in groupChildren['custom-layout-2-repeater']['children'] %}
                {% set repeaterGroupValues = repeaterGroup['children'] %}
                <li>
                    <strong>{{ repeaterGroupValues['repeater-title']['raw'] }}</strong>:
                    {{ repeaterGroupValues['repeater-description']['raw']|raw }} {# "|raw" to display wysiwyg content #}
                </li>
            {% endfor %}
        </ul>
    </section>
{% endmacro %}
```

If you want to display only a given field, you can also access it directly :
 
```twig
{#templates/content.html.twig#}

{% set field = acb_field(content, 'slug') %}

<h1>{{ field['raw'] }}</h1>
```

If we use acb_fields on the content of the page example [custom_page.yaml](import/Page/custom_page.yaml), it will return : 

```php
[
    'custom-text-field' => [
        'fieldValue' => object(FieldValue),
        'raw' => 'Text value',
    ],
    'custom-flexible-field' => [
        'fieldValue' => object(FieldValue),
        'raw' => '',
        'children' => [
            0 => [
                'fieldGroupValue' => object(FieldGroupValue),
                'name' => 'Custom Layout 1',
                'children' => [
                    'custom-layout-1-title' => [
                        'fieldValue' => object(FieldValue),
                        'raw' => 'Title value',
                    ],
                ],
            ],
            1 => [
                'fieldGroupValue' => object(FieldGroupValue),
                'name' => 'Custom Layout 2',
                'children' => [
                    'custom-layout-2-title' => [
                        'fieldValue' => object(FieldValue),
                        'raw' => 'Title layout value',
                    ],
                    'custom-layout-2-repeater' => [
                        'fieldValue' => object(FieldValue),
                        'raw' => 'Title layout value',
                        'children' => [
                            0 => [
                                'fieldGroupValue' => object(FieldGroupValue),
                                'name' => 'Custom Layout 2 - Repeater',
                                'children' => [
                                    'repeater-title' => [
                                        'fieldValue' => object(FieldValue),
                                        'raw' => 'First group of repeater - title',
                                    ],
                                    'repeater-description' => [
                                        'fieldValue' => object(FieldValue),
                                        'raw' => '<strong>First</strong> group of repeater - description',
                                    ],
                                ],
                            ],
                            1 => [
                                'fieldGroupValue' => object(FieldGroupValue),
                                'name' => 'Custom Layout 2 - Repeater',
                                'children' => [
                                    'repeater-title' => [
                                        'fieldValue' => object(FieldValue),
                                        'raw' => 'Second group of repeater - title',
                                    ],
                                    'repeater-description' => [
                                        'fieldValue' => object(FieldValue),
                                        'raw' => 'Second group of repeater <br/> description',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

If we use acb_field on the content of the page example [custom_page.yaml](import/Page/custom_page.yaml) for slug "custom-text-field", it will return : 

```php
[
    'fieldValue' => object(FieldValue),
    'raw' => 'Text value',
];
```
