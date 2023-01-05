<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sherlockode\AdvancedContentBundle\Form\Type\AcbContentType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

class Content extends AbstractFieldType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        EntityManagerInterface $em
    ) {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbContentType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'content';
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return array
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        $value = $fieldValue->getValue();
        $value['entity'] = null;

        $contentId = $value['content'] ?? null;
        if ($contentId === null) {
            return $value;
        }

        $content = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->find($contentId);
        if ($content === null) {
            return $value;
        }

        $parentContent = $fieldValue->getContent();
        if ($parentContent !== null && $parentContent->getId() === $content->getId()) {
            return $value;
        }

        $value['entity'] = $content;

        return $value;
    }
}
