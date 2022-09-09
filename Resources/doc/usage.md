Usage
======

## Controller

### Page

To display content linked to a given page, you can have the following controller: 

```php
<?php
// App/Controller/PageController
namespace App\Controller;

use App\Entity\PageMeta;
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
     * @param PageMeta $pageMeta
     *
     * @return Response
     */
    public function indexAction(PageMeta $pageMeta)
    {
        $page = $pageMeta->getPage();
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

If your content is not linked to any page (for example, your homepage), you can retrieve it by using its slug.

## Twig

The bundle provides a twig function that will return a string or array containing formatted data for a given content.

```twig
{% set rawData = acb_render_field(content.myField) %}
```

Depending on the type and structure of the field, the data will be formatted differently.
Each class inheriting the FieldTypeInterface must implement the `getRawValue()` for this purpose.
