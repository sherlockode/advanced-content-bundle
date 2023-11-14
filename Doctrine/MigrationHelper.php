<?php

namespace Sherlockode\AdvancedContentBundle\Doctrine;

use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

class MigrationHelper implements MigrationHelperInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @param EntityManagerInterface $em
     * @param array                  $mapping
     */
    public function __construct(EntityManagerInterface $em, array $mapping)
    {
        $this->em = $em;
        $this->mapping = $mapping;
    }

    /**
     * @param string $slug
     *
     * @return int|null
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function getContentIdBySlug(string $slug): ?int
    {
        $connection = $this->em->getConnection();
        $query = $connection->prepare(sprintf(
            'SELECT c.id FROM %s c WHERE c.page_id IS NULL AND c.slug = :slug',
            $this->getTableName('content')
        ));
        $stmt = $query->executeQuery(['slug' => $slug]);

        if (!$stmt->rowCount()) {
            return null;
        }

        $content = $stmt->fetchAssociative();

        return $content['id'];
    }

    /**
     * @param string $slug
     *
     * @return array|null
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function readContent(string $slug): ?array
    {
        $query = $this->em->getConnection()->prepare(sprintf(
            'SELECT cv.data FROM %s c INNER JOIN %s cv ON c.content_version_id = cv.id ' .
                   'WHERE c.page_id IS NULL AND c.slug = :slug',
            $this->getTableName('content'),
            $this->getTableName('content_version')
        ));
        $stmt = $query->executeQuery(['slug' => $slug]);
        $row = $stmt->fetchAssociative();

        $raw = isset($row['data']) ? $row['data'] : null;

        if ($raw) {
            return json_decode($raw, true);
        }

        return null;
    }

    /**
     * @param string      $slug
     * @param array       $data
     * @param string|null $name
     *
     * @return int
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function writeContent(string $slug, array $data, ?string $name = null): int
    {
        $connection = $this->em->getConnection();
        $contentId = $this->getContentIdBySlug($slug);
        $contentTable = $this->getTableName('content');

        if (!$contentId) {
            $connection->insert(
                $contentTable,
                [
                    'name' => isset($name) ? $name : $slug,
                    'slug' => $slug,
                ]
            );
            $contentId = $connection->lastInsertId();
        }

        $connection->insert(
            $this->getTableName('content_version'),
            [
                'content_id' => $contentId,
                'data' => json_encode($data),
                'created_at' => date('Y-m-d H:i:s'),
                'auto_save' => 0,
            ]
        );
        $updateData = ['content_version_id' => $connection->lastInsertId()];

        if ($name) {
            $updateData['name'] = $name;
        }

        $connection->update($contentTable, $updateData, ['id' => $contentId]);

        return $contentId;
    }

    /**
     * @param string $slug
     *
     * @return void
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function removeContent(string $slug): void
    {
        $connection = $this->em->getConnection();
        $contentId = $this->getContentIdBySlug($slug);

        $connection->delete($this->getTableName('content_version'), ['content_id' => $contentId]);
        $connection->delete($this->getTableName('content'), ['id' => $contentId]);
    }

    /**
     * @param string $identifier
     *
     * @return int|null
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function getPageIdByIdentifier(string $identifier): ?int
    {
        $connection = $this->em->getConnection();
        $query = $connection->prepare(sprintf(
            'SELECT p.id FROM %s p WHERE p.page_identifier = :identifier',
            $this->getTableName('page')
        ));
        $stmt = $query->executeQuery(['identifier' => $identifier]);

        if (!$stmt->rowCount()) {
            return null;
        }

        $page = $stmt->fetchAssociative();

        return $page['id'];
    }

    /**
     * @param string $identifier
     *
     * @return array|null
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function readPage(string $identifier): ?array
    {
        $query = $this->em->getConnection()->prepare(sprintf(
            'SELECT cv.data FROM %s p INNER JOIN %s pv ON p.page_version_id = pv.id ' .
            'INNER JOIN %s cv ON pv.content_version_id = cv.id ' .
            'WHERE p.page_identifier = :identifier',
            $this->getTableName('page'),
            $this->getTableName('page_version'),
            $this->getTableName('content_version')
        ));
        $stmt = $query->executeQuery(['identifier' => $identifier]);
        $row = $stmt->fetchAssociative();

        $raw = isset($row['data']) ? $row['data'] : null;

        if ($raw) {
            return json_decode($raw, true);
        }

        return null;
    }

    /**
     * @param string      $identifier
     * @param array       $data
     * @param string|null $name
     * @param int         $status
     *
     * @return int
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function writePage(
        string $identifier,
        array $data,
        ?string $name = null,
        int $status = PageInterface::STATUS_DRAFT
    ): int {
        $now = date('Y-m-d H:i:s');
        $connection = $this->em->getConnection();
        $pageTable = $this->getTableName('page');
        $pageMetaTable = $this->getTableName('page_meta');
        $contentTable = $this->getTableName('content');

        $pageId = $this->getPageIdByIdentifier($identifier);

        if (!$pageId) {
            $connection->insert($pageTable, [
                'page_identifier' => $identifier,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $pageId = $connection->lastInsertId();
        } else {
            $connection->update($pageTable, ['status' => $status, 'updated_at' => $now], ['id' => $pageId]);
        }

        $query = $connection->prepare(sprintf('SELECT c.id FROM %s c WHERE c.page_id = :pageId', $contentTable));
        $stmt = $query->executeQuery(['pageId' => $pageId]);

        if (!$stmt->rowCount()) {
            $connection->insert($contentTable, [
                'page_id' => $pageId,
                'name' => isset($name) ? $name : $identifier,
                'slug' => $identifier,
            ]);
            $contentId = $connection->lastInsertId();
        } else {
            $content = $stmt->fetchAssociative();
            $contentId = $content['id'];
        }

        $connection->insert($this->getTableName('content_version'), [
            'content_id' => $contentId,
            'data' => json_encode($data),
            'created_at' => $now,
            'auto_save' => 0,
        ]);
        $contentVersionId = $connection->lastInsertId();

        $query = $connection->prepare(sprintf('SELECT m.id FROM %s m WHERE m.page_id = :pageId', $pageMetaTable));
        $stmt = $query->executeQuery(['pageId' => $pageId]);

        if (!$stmt->rowCount()) {
            $connection->insert($pageMetaTable, ['page_id' => $pageId]);
            $metaId = $connection->lastInsertId();
        } else {
            $content = $stmt->fetchAssociative();
            $metaId = $content['id'];
        }

        $connection->insert($this->getTableName('page_meta_version'), [
            'page_meta_id' => $metaId,
            'title' => isset($name) ? $name : $identifier,
            'slug' => $identifier,
            'created_at' => $now,
            'auto_save' => 0,
        ]);
        $metaVersionId = $connection->lastInsertId();

        $connection->insert($this->getTableName('page_version'), [
            'page_id' => $pageId,
            'content_version_id' => $contentVersionId,
            'page_meta_version_id' => $metaVersionId,
            'created_at' => $now,
            'auto_save' => 0,
        ]);
        $pageVersionId = $connection->lastInsertId();

        $connection->update($pageTable, ['page_version_id' => $pageVersionId], ['id' => $pageId]);

        return $pageId;
    }

    /**
     * @param string $identifier
     *
     * @return void
     *
     * @throws DriverException
     * @throws DBALException
     */
    public function removePage(string $identifier): void
    {
        $connection = $this->em->getConnection();
        $contentTable = $this->getTableName('content');
        $pageMetaTable = $this->getTableName('page_meta');

        $pageId = $this->getPageIdByIdentifier($identifier);

        if (!$pageId) {
            return;
        }

        $query = $connection->prepare(sprintf('SELECT c.id FROM %s c WHERE c.page_id = :pageId', $contentTable));
        $stmt = $query->executeQuery(['pageId' => $pageId]);
        if ($stmt->rowCount()) {
            $content = $stmt->fetchAssociative();
            $contentId = $content['id'];

            $connection->delete($this->getTableName('content_version'), ['content_id' => $contentId]);
            $connection->delete($contentTable, ['id' => $contentId]);
        }

        $query = $connection->prepare(sprintf('SELECT m.id FROM %s m WHERE m.page_id = :pageId', $pageMetaTable));
        $stmt = $query->executeQuery(['pageId' => $pageId]);
        if ($stmt->rowCount()) {
            $content = $stmt->fetchAssociative();
            $metaId = $content['id'];

            $connection->delete($this->getTableName('page_meta_version'), ['page_meta_id' => $metaId]);
            $connection->delete($pageMetaTable, ['id' => $metaId]);
        }

        $connection->delete($this->getTableName('page_version'), ['page_id' => $pageId]);
        $connection->delete($this->getTableName('page'), ['id' => $pageId]);
    }

    /**
     * @param string $defaultTableName
     *
     * @return string|null
     *
     * @throws \Exception
     */
    private function getTableName(string $defaultTableName): ?string
    {
        if (!isset($this->mapping[$defaultTableName])) {
            throw new \Exception(sprintf('Could not find corresponding entity for "%s" element', $defaultTableName));
        }

        $metadata = $this->em->getClassMetadata($this->mapping[$defaultTableName]);

        if (!isset($metadata->table['name'])) {
            throw new \Exception(sprintf('Cannot read table name for entity "%s"', $defaultTableName));
        }

        return $metadata->table['name'];
    }
}
