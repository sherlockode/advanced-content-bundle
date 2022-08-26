<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\AcbFileType;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\DataTransformerInterface;

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

    /**
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        return ['uploadManager' => $this->uploadManager];
    }

    /**
     * Get model transformer for value field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer()
    {
        return new StringToArrayTransformer();
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
     * Render field value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function render(FieldValueInterface $fieldValue)
    {
        $value = $fieldValue->getValue();
        $value = unserialize($value);

        $fileName = $this->getFilename($value);
        if ($fileName === '') {
            return '';
        }

        return $this->renderFile($fileName, $value);
    }

    /**
     * @param string $fileName
     * @param string $value
     *
     * @return string
     */
    protected function renderFile($fileName, $value)
    {
        $title = $value['title'] ?? $value['src'];

        return '<a href="' . $fileName . '" title="' . $title . '" download>' . $value['src'] . '</a>';
    }

    /**
     * @param array $value
     *
     * @return string
     */
    private function getFilename($value)
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
        $value = unserialize($fieldValue->getValue());
        $value['url'] = $this->getFilename($value);

        if (isset($value['delete'])) {
            unset($value['delete']);
        }

        return $value;
    }
}
