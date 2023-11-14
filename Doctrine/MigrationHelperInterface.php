<?php

namespace Sherlockode\AdvancedContentBundle\Doctrine;

interface MigrationHelperInterface
{
    /**
     * @param string $slug
     *
     * @return int|null
     */
    public function getContentIdBySlug(string $slug): ?int;

    /**
     * @param string $slug
     *
     * @return array|null
     */
    public function readContent(string $slug): ?array;

    /**
     * @param string      $slug
     * @param array       $data
     * @param string|null $name
     *
     * @return int
     */
    public function writeContent(string $slug, array $data, ?string $name = null): int;

    /**
     * @param string $slug
     *
     * @return void
     */
    public function removeContent(string $slug): void;

    /**
     * @param string $identifier
     *
     * @return int|null
     */
    public function getPageIdByIdentifier(string $identifier): ?int;

    /**
     * @param string $identifier
     *
     * @return array|null
     */
    public function readPage(string $identifier): ?array;

    /**
     * @param string      $identifier
     * @param array       $data
     * @param string|null $name
     *
     * @return int
     */
    public function writePage(string $identifier, array $data, ?string $name = null): int;

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function removePage(string $identifier): void;
}
