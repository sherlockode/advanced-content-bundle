# Data migration

----

In order to help you to edit pages and contents during the lifecycle of a project
we have created multiple helpers. These are made for [Doctrine migrations](https://www.doctrine-project.org/projects/doctrine-migrations-bundle/en/3.2/index.html).

First, you have to inject the helpers service in your migration, and then you 
can use it to edit contents and pages.

## Inject the helpers

Doctrine let us injecting services in our migrations by decorating the migration factory.
Here is an example of our factory:

```php
<?php

namespace App\Migrations\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperInterface;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperAwareInterface;

class MigrationFactoryDecorator implements MigrationFactory
{
    /**
     * @param MigrationFactory         $migrationFactory
     * @param MigrationHelperInterface $helper
     */
    public function __construct(
        private readonly MigrationFactory $migrationFactory,
        private readonly MigrationHelperInterface $helper
    ) {
    }

    /**
     * @param string $migrationClassName
     *
     * @return AbstractMigration
     */
    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $instance = $this->migrationFactory->createVersion($migrationClassName);

        if ($instance instanceof MigrationHelperAwareInterface) {
            $instance->setHelper($this->helper);
        }

        return $instance;
    }
}
```

And of course, you have to configure your services:

```yaml
# config/services.yaml

services:
    Doctrine\Migrations\Version\DbalMigrationFactory: ~
    App\Migrations\Factory\MigrationFactoryDecorator:
        decorates: Doctrine\Migrations\Version\DbalMigrationFactory
        arguments:
            - '@App\Migrations\Factory\MigrationFactoryDecorator.inner'
            - '@sherlockode_advanced_content.migrations_helper'
```

That's it: now you just have to implement the `MigrationHelperAwareInterface` on your
migration, and you can start using the helpers.

You can learn more about service injection in Doctrine Migrations on [the dedicated page of
their documentation](https://www.doctrine-project.org/projects/doctrine-migrations-bundle/en/3.2/index.html#migration-dependencies).

## Content edition

Here is an example of a migration which manipulate a content:

```php
<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperAwareInterface;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperInterface;

final class Version20230911094832 extends AbstractMigration implements MigrationHelperAwareInterface
{
    /**
     * @var MigrationHelperInterface|null
     */
    private ?MigrationHelperInterface $helper;

    /**
     * @param MigrationHelperInterface $helper
     *
     * @return $this
     */
    public function setHelper(MigrationHelperInterface $helper): self
    {
        $this->helper = $helper;

        return $this;
    }

    public function up(Schema $schema): void
    {
        $contentData = [
            [
                'elementType' => 'row',
                'position' => '0',
                'extra' => [],
                'config' => [],
                'elements' => [
                    [
                        'elementType' => 'column',
                        'position' => '0',
                        'extra' => [],
                        'config' => ['size' => 12],
                        'elements' => [],
                    ]
                ]
            ]
        ];

        // Retrieve the ID of the content object
        $contentId = $this->helper->getContentIdBySlug('my_content_identifier');
        
        // If the content does not exists, let's create it
        if (!$contentId) {
            $contentId = $this->helper->writeContent(
                'my_content_identifier',
                $contentData,
                'The title of my content'
            );
        }
        
        // Retrieve the content data
        $oldContentData = $this->helper->readContent('my_content_identifier');
        
        // Now let's update the content data
        $newContentData = array_merge($oldContentData, $contentData);
        $this->helper->writeContent(
            'my_content_identifier',
            $newContentData,
            'The new title of my content'
        );
    }

    public function down(Schema $schema): void
    {
        // Remove the content 
        $this->helper->removeContent('my_content_identifier');
    }
}

```

## Page edition

Now you can apply the same process to edit a page:

```php
<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperAwareInterface;
use Sherlockode\AdvancedContentBundle\Doctrine\MigrationHelperInterface;

final class Version20230911094848 extends AbstractMigration implements MigrationHelperAwareInterface
{
    /**
     * @var MigrationHelperInterface|null
     */
    private ?MigrationHelperInterface $helper;

    /**
     * @param MigrationHelperInterface $helper
     *
     * @return $this
     */
    public function setHelper(MigrationHelperInterface $helper): self
    {
        $this->helper = $helper;

        return $this;
    }

    public function up(Schema $schema): void
    {
        $pageData = [
            [
                'elementType' => 'row',
                'position' => '0',
                'extra' => [],
                'config' => [],
                'elements' => [
                    [
                        'elementType' => 'column',
                        'position' => '0',
                        'extra' => [],
                        'config' => ['size' => 12],
                        'elements' => [],
                    ]
                ]
            ]
        ];

        // Retrieve the ID of the page object
        $pageId = $this->helper->getPageIdByIdentifier('my_page_identifier');
        
        // If the page does not exists, let's create it
        if (!$pageId) {
            $pageId = $this->helper->writePage(
                'my_page_identifier',
                $pageData,
                'The title of my awesome page !'
            );
        }
        
        // Retrieve the page data
        $oldPageData = $this->helper->readPage('my_page_identifier');
        
        // Now let's update the page data
        $newPageData = array_merge($oldPageData, $pageData);
        $this->helper->writePage(
            'my_page_identifier',
            $newPageData,
            'The new title of my awesome page'
        );
    }

    public function down(Schema $schema): void
    {
        // Remove the page
        $this->helper->removePage('my_page_identifier');
    }
}

```
