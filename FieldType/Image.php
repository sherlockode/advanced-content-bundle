<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\ImageType;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\DataTransformerInterface;

class Image extends AbstractFieldType
{
    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var Packages
     */
    private $assetPackages;

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
        return ImageType::class;
    }

    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field)
    {
        return ['field' => $field, 'uploadManager' => $this->uploadManager];
    }

    /**
     * Get model transformer for value field
     *
     * @param FieldInterface $field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer(FieldInterface $field)
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
        return 'image';
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

        if (empty($value['src'])) {
            return '';
        }

        $fileName = $this->uploadManager->getTargetDir() . DIRECTORY_SEPARATOR . $value['src'];
        if (!file_exists($fileName)) {
            return '';
        }

        $fileName = $this->assetPackages->getUrl($this->uploadManager->getWebPath() . '/' . $value['src']);

        return '<img src="' . $fileName . '" alt="' . $value['alt'] . '"/>';
    }

    /**
     * Update fieldValue value before saving it
     *
     * @param FieldValueInterface $fieldValue
     * @param array               $changeSet
     *
     * @return void
     */
    public function updateFieldValueValue(FieldValueInterface $fieldValue, $changeSet)
    {
        if (!isset($changeSet['value'])) {
            return;
        }

        $oldValue = unserialize($changeSet['value'][0]);
        $newValue = unserialize($changeSet['value'][1]);

        if ($newValue['src'] == '' && !$newValue['delete']) {
            $newValue['src'] = $oldValue['src'];
        }

        if ($newValue['delete']) {
            $this->uploadManager->remove($oldValue['src']);
        }

        unset($newValue['delete']);

        $fieldValue->setValue(serialize($newValue));
    }
}
