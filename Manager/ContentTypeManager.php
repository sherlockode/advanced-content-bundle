<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ContentTypeManager
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * ContentManager constructor.
     *
     * @param ConfigurationManager $configurationManager
     * @param ObjectManager        $om
     */
    public function __construct(ConfigurationManager $configurationManager, ObjectManager $om)
    {
        $this->configurationManager = $configurationManager;
        $this->om = $om;
    }

    /**
     * Get content type by its id
     *
     * @param int $id
     *
     * @return null|ContentTypeInterface
     */
    public function getContentTypeById($id)
    {
        return $this->om->getRepository($this->configurationManager->getEntityClass('content_type'))->find($id);
    }
}
