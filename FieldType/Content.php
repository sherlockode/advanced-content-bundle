<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sherlockode\AdvancedContentBundle\Form\Type\AcbContentType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;

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
     * @param mixed $element
     *
     * @return array
     */
    public function getRawValue($element)
    {
        $element['entity'] = null;

        $contentId = $element['content'] ?? null;
        if ($contentId === null) {
            return $element;
        }

        $content = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->find($contentId);
        if ($content === null) {
            return $element;
        }

        $element['entity'] = $content;

        return $element;
    }
}
