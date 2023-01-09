Entities
========

## Basics

We implemented the following entity models:
- Content
- PageType
- Page
- PageMeta

See the list of available field types [here](field_types.md)

When you create a new Page you will be able to fill in your Content inside.

## Implementation examples

If you need to override the entities with your own, here are the native implementations used within the bundle:

```php
<?php
// src/Entity/Content.php

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="contents")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $page;
}

```

```php
<?php
// src/Entity/Page.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\Page as BasePage;

/**
 * @ORM\Entity
 * @ORM\Table(name="page")
 */
class Page extends BasePage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Content", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $contents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PageType")
     * @ORM\JoinColumn(name="page_type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $pageType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PageMeta", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $pageMetas;
}
```

```php
<?php
// src/Entity/PageType.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\PageType as BasePageType;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_type")
 */
class PageType extends BasePageType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

```php
<?php
// src/Entity/PageMeta.php

namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\PageMeta as BasePageMeta;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_meta")
 */
class PageMeta extends BasePageMeta
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="pageMetas")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;
}
```
