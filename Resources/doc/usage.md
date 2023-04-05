# Usage

----

## Page

----

To display a page, you can use the following controller and template: 

```php
<?php
// src/Controller/PageController.php

namespace App\Controller;

use App\Entity\Page;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/{slug}", name="page_view")
     *
     * @param Request $request
     * @param ScopeHandlerInterface $scopeHandler 
     *
     * @return Response
     */
    public function indexAction(Request $request, ScopeHandlerInterface $scopeHandler)
    {
        $page = $em->getRepository(Page::class)->findOneBySlug(
            $request->attributes->get('slug'),
            $scopeHandler->getCurrentScope()
        );
        if ($page->getStatus() !== PageInterface::STATUS_PUBLISHED || !$page->getContent() instanceof ContentInterface) {
            throw $this->createNotFoundException();
        }

        return $this->render('page.html.twig', [
            'page' => $page,
        ]);
    }
}
```

```twig
{# templates/page.html.twig #}

{% extends 'layout.html.twig' %}

{% block title %}
    {% if page.pageMeta is not null and page.pageMeta.metaTitle %}
        {{ page.pageMeta.metaTitle }}
    {% else %}
        {{ parent() }}
    {% endif %}
{% endblock %}

{% block metatag_description %}
    {% if page.pageMeta is not null and page.pageMeta.metaDescription %}
        <meta name="description" content="{{ page.pageMeta.metaDescription }}">
    {% endif %}
{% endblock %}

{% block content %}
    {% for element in page.content.data %}
        {{ acb_render_element(element) }}
    {% endfor %}
{% endblock %}

```

## Content

----

If your content is not linked to any page (for example, your homepage), you can retrieve it by using its slug.
If scopes management is not enabled, this method will return the single content linked to the slug.
If scopes management is enabled, this method will return the content linked to the slug and to the current front scope (current locale).
If no content is found, the twig function will return null.

```twig
{% set content = acb_get_content_by_slug(slug) %}
{% if content is not null %}
    {% for element in content.data %}
        {{ acb_render_element(element) }}
    {% endfor %}
{% endif %}
```
