<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentManager
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
     * ContentManager constructor.
     *
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     */
    public function __construct(ConfigurationManager $configurationManager, EntityManagerInterface $em)
    {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    /**
     * Get content by its id
     *
     * @param int $id
     *
     * @return null|ContentInterface
     */
    public function getContentById($id)
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->find($id);
    }

    /**
     * Get all contents
     *
     * @return array
     */
    public function getContents()
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findAll();
    }
}
