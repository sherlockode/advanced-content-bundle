<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Manager;

use Symfony\Contracts\Translation\TranslatorInterface;

class MimeTypeManager
{
    public const MIME_TYPE_IMAGE = 10;

    public const MIME_TYPE_PDF = 20;

    public const MIME_TYPE_EXE = 30;

    public const MIME_TYPE_ARCHIVE = 40;

    public const MIME_TYPE_TEXT = 50;

    public const MIME_TYPE_SPREADSHEET = 60;

    public const MIME_TYPE_MULTIMEDIA = 70;

    public function __construct(
        private array $mimeTypes,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return int[]
     */
    public function generateMimeTypeChoices(): array
    {
        return [
            'field_type.mime_type_restriction.pdf' => self::MIME_TYPE_PDF,
            'field_type.mime_type_restriction.image' => self::MIME_TYPE_IMAGE,
            'field_type.mime_type_restriction.multimedia' => self::MIME_TYPE_MULTIMEDIA,
            'field_type.mime_type_restriction.archive' => self::MIME_TYPE_ARCHIVE,
            'field_type.mime_type_restriction.spreadsheet' => self::MIME_TYPE_SPREADSHEET,
            'field_type.mime_type_restriction.text' => self::MIME_TYPE_TEXT,
            'field_type.mime_type_restriction.exe' => self::MIME_TYPE_EXE,
        ];
    }

    public function getMimeTypesByCode(int|string $code): array
    {
        $mimeTypes = [
            self::MIME_TYPE_IMAGE => 'sherlockode_advanced_content.mime_type_group.image',
            self::MIME_TYPE_PDF => 'sherlockode_advanced_content.mime_type_group.pdf',
            self::MIME_TYPE_EXE => 'sherlockode_advanced_content.mime_type_group.executable',
            self::MIME_TYPE_ARCHIVE => 'sherlockode_advanced_content.mime_type_group.archive',
            self::MIME_TYPE_TEXT => 'sherlockode_advanced_content.mime_type_group.text_file',
            self::MIME_TYPE_SPREADSHEET => 'sherlockode_advanced_content.mime_type_group.spreadsheet',
            self::MIME_TYPE_MULTIMEDIA => 'sherlockode_advanced_content.mime_type_group.multimedia',
        ];

        if (!isset($mimeTypes[$code])) {
            $imageMimeTypes = $this->getImageMimeTypesChoices();

            if ('*' === $code) {
                return ['image/*'];
            }

            return ['image/'.$imageMimeTypes[$code]] ?? [];
        }

        return $this->mimeTypes[$mimeTypes[$code]];
    }

    public function getImageMimeTypesChoices(): array
    {
        $types = $this->getMimeTypesByCode(self::MIME_TYPE_IMAGE);
        $extensions = [];

        if (1 === count($types) && 'image/*' === $types[0]) {
            $extensions['*'] = $this->translator->trans('field_type.mime_type_restriction.image_all_types', [], 'AdvancedContentBundle');

            return $extensions;
        }

        foreach ($types as $type) {
            $extensions[] = basename((string) $type);
        }

        return array_combine($extensions, $extensions);
    }

    public function getAllMimeTypes(): array
    {
        return array_merge([], ...array_values($this->mimeTypes));
    }
}
