<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Doctrine;

interface MigrationHelperInterface
{
    public function getContentIdBySlug(string $slug): ?int;

    public function readContent(string $slug): ?array;

    public function writeContent(string $slug, array $data, ?string $name = null): int;

    public function removeContent(string $slug): void;

    public function getPageIdByIdentifier(string $identifier): ?int;

    public function readPage(string $identifier): ?array;

    public function writePage(string $identifier, array $data, ?string $name = null): int;

    public function removePage(string $identifier): void;
}
