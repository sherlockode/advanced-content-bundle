Entities
========

## Basics

We implemented the following models:
- Content
- ContentVersion
- PageType
- Page
- PageVersion
- PageMeta
- PageMetaVersion

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
     * @ORM\OneToOne(targetEntity="App\Entity\Page", inversedBy="content")
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

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Scope")
     * @ORM\JoinTable(name="content_scope",
     *      joinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="scope_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $scopes;
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
// src/Entity/Scope.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\LocaleScope;

/**
 * @ORM\Entity
 * @ORM\Table(name="scope")
 */
class Scope extends LocaleScope
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string")
     */
    protected $locale;
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
     * @ORM\OneToOne(targetEntity="App\Entity\Content", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PageType")
     * @ORM\JoinColumn(name="page_type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $pageType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PageMeta", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $pageMeta;

    /**
     * @var PageVersionInterface
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PageVersion")
     * @ORM\JoinColumn(name="page_version_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $pageVersion;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PageVersion", mappedBy="page", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    protected $versions;
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
     * @ORM\OneToOne(targetEntity="App\Entity\Page", inversedBy="pageMeta")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PageMetaVersion", mappedBy="pageMeta", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    protected $versions;
}
```

```php
<?php
// src/Entity/PageMetaVersion.php

namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\PageMetaVersion as BasePageMetaVersion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_meta_version")
 */
class PageMetaVersion extends BasePageMetaVersion
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
     * @ORM\ManyToOne(targetEntity="App\Entity\PageMeta", inversedBy="versions")
     * @ORM\JoinColumn(name="page_meta_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $pageMeta;
}
```

```php
<?php
// src/Entity/PageVersion.php

namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\PageVersion as BasePageVersion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_version")
 */
class PageVersion extends BasePageVersion
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="versions")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ContentVersion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="content_version_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $contentVersion;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PageMetaVersion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="page_meta_version_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $pageMetaVersion;
}
```
