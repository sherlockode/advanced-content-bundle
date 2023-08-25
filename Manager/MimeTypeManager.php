<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

class MimeTypeManager
{
    const MIME_TYPE_IMAGE = 10;
    const MIME_TYPE_PDF = 20;
    const MIME_TYPE_EXE = 30;
    const MIME_TYPE_ARCHIVE = 40;
    const MIME_TYPE_TEXT = 50;
    const MIME_TYPE_SPREADSHEET = 60;
    const MIME_TYPE_MULTIMEDIA = 70;

    /**
     * @var array
     */
    private $mimeTypes;

    /**
     * @param array $mimeTypes
     */
    public function __construct(array $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
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

    /**
     * @param int $code
     *
     * @return array
     */
    public function getMimeTypesByCode(int $code): array
    {
        $mimeTypes = [
            self::MIME_TYPE_IMAGE => 'sherlockode_advanced_content.mime_type_group.image',
            self::MIME_TYPE_PDF => 'sherlockode_advanced_content.mime_type_group.pdf',
            self::MIME_TYPE_EXE => 'sherlockode_advanced_content.mime_type_group.executable',
            self::MIME_TYPE_ARCHIVE => 'sherlockode_advanced_content.mime_type_group.archive',
            self::MIME_TYPE_TEXT => 'sherlockode_advanced_content.mime_type_group.text_file',
            self::MIME_TYPE_SPREADSHEET => 'sherlockode_advanced_content.mime_type_group.spreadsheet',
            self::MIME_TYPE_MULTIMEDIA => 'sherlockode_advanced_content.mime_type_group.multimedia'
        ];

        if (!isset($mimeTypes[$code])) {
            return [];
        }

        return $this->mimeTypes[$mimeTypes[$code]];
    }
}
