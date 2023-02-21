Entities
========

## Basics

We implemented the following models:
- Content
- ContentVersion
- PageType
- Page
- PageMeta

See the list of available field types [here](field_types.md)

When you create a new Page you will be able to fill in your Content inside.

## Implementation examples

Here are basic example implementations for the entity classes:

```php
<?php
// src/Entity/Content.php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\Content as BaseContent;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;

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

    /**
     * @var ContentVersionInterface
     *
     * @ORM\OneToOne(targetEntity="App\Entity\ContentVersion")
     * @ORM\JoinColumn(name="content_version_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $contentVersion;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ContentVersion", mappedBy="content", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    protected $versions;
}

```

```php
<?php
// src/Entity/ContentVersion.php

namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\ContentVersion as BaseContentVersion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_version")
 */
class ContentVersion extends BaseContentVersion
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Content", inversedBy="versions")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $content;
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
