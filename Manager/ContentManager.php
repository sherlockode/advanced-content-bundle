<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Slug\SlugProviderInterface;

class ContentManager
{
    /**
     * ContentManager constructor.
     */
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly EntityManagerInterface $em,
        private readonly SlugProviderInterface $slugProvider,
    ) {
    }

    /**
     * Get content by its id.
     *
     * @param int $id
     *
     * @return ContentInterface|null
     */
    public function getContentById($id)
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->find($id);
    }

    /**
     * Get all contents.
     *
     * @return array
     */
    public function getContents()
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findAll();
    }

    public function duplicate(ContentInterface $content): ContentInterface
    {
        $newContent = clone $content;
        $this->slugProvider->setContentValidSlug($newContent);
        $newContent->setPage(null);

        return $newContent;
    }
}
