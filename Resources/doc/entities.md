Entities
========

Here are basic example implementations for the entity classes:


```php
<?php
// src/AppBundle/Entity/ContentType.php

namespace AppBundle\Entity;

use Sherlockode\AdvancedContentBundle\Model\ContentType as BaseContentType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type")
 */
class ContentType extends BaseContentType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Field", mappedBy="contentType", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Content", mappedBy="contentType", cascade={"persist", "remove"})
     */
    protected $contents;
}
```

```php
<?php
// src/AppBundle/Entity/Field.php
namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\Field as BaseField;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="field")
 */
class Field extends BaseField
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContentType", inversedBy="fields")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FieldValue", mappedBy="field", cascade={"persist", "remove"})
     */
    protected $fieldValues;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Layout", inversedBy="children")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $layout;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Layout", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $children;
}
```

```php
<?php
// src/AppBundle/Entity/Content.php

namespace AppBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ContentType", inversedBy="contents")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FieldValue", mappedBy="content", cascade={"persist", "remove"})
     */
    protected $fieldValues;
}
```

```php
<?php
// src/AppBundle/Entity/FieldValue.php

namespace App\Entity;

use Sherlockode\AdvancedContentBundle\Model\FieldValue as BaseFieldValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="field_value")
 */
class FieldValue extends BaseFieldValue
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
     * @var Content
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Content", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;

    /**
     * @var Field
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Field", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;

    /**
     * @var FieldGroupValue
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FieldGroupValue", inversedBy="children")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $group;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FieldGroupValue", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $children;
}
```

```php
<?php
// src/AppBundle/Entity/FieldGroupValue.php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValue as BaseFieldGroupValue;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="field_group_value")
 */
class FieldGroupValue extends BaseFieldGroupValue
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
     * @var FieldValueInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FieldValue", inversedBy="children")
     */
    protected $parent;

    /**
     * @var FieldValueInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FieldValue", mappedBy="group", cascade={"persist", "remove"})
     */
    protected $children;

    /**
     * @var LayoutInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Layout", inversedBy="fieldGroupValues")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $layout;
}
```

```php
<?php
// src/AppBundle/Entity/Layout.php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\Layout as BaseLayout;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="layout")
 */
class Layout extends BaseLayout
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
     * @var FieldInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Field", inversedBy="children")
     */
    protected $parent;

    /**
     * @var FieldInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Field", mappedBy="layout", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $children;

    /**
     * @var FieldGroupValueInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FieldGroupValue", mappedBy="layout", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $fieldGroupValues;
}
```
