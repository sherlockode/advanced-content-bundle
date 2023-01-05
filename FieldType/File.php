<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbFileType;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Asset\Packages;

class File extends AbstractFieldType
{
    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var Packages
     */
    private $assetPackages;

    /**
     * @param UploadManager $uploadManager
     * @param Packages      $packages
     */
    public function __construct(UploadManager $uploadManager, Packages $packages)
    {
        $this->uploadManager = $uploadManager;
        $this->assetPackages = $packages;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbFileType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-paperclip';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'file';
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function getFilename($value)
    {
        if (empty($value['src'])) {
            return '';
        }

        $fileName = $this->uploadManager->getTargetDir() . DIRECTORY_SEPARATOR . $value['src'];
        if (!file_exists($fileName)) {
            return '';
        }

        $fileName = $this->assetPackages->getUrl($this->uploadManager->getWebPath() . '/' . $value['src']);

        return $fileName;
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        $value = $fieldValue->getValue();
        $value['url'] = $this->getFilename($value);

        if (isset($value['delete'])) {
            unset($value['delete']);
        }

        return $value;
    }
}
