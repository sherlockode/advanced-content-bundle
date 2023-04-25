<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Slug\SlugProviderInterface;

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
     * @var SlugProviderInterface
     */
    private $slugProvider;

    /**
     * ContentManager constructor.
     *
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     * @param SlugProviderInterface  $slugProvider
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        EntityManagerInterface $em,
        SlugProviderInterface $slugProvider
    ) {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
        $this->slugProvider = $slugProvider;
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

    /**
     * @param ContentInterface $content
     *
     * @return ContentInterface
     */
    public function duplicate(ContentInterface $content): ContentInterface
    {
        $newContent = clone $content;
        $this->slugProvider->setContentValidSlug($newContent);
        $newContent->setPage(null);

        return $newContent;
    }
}
