<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageManager
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
    public function __construct(ConfigurationManager $configurationManager, EntityManagerInterface $em)
    {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    /**
     * @param PageTypeInterface $pageType
     *
     * @return bool
     */
    public function updatePagesAfterPageTypeRemove(PageTypeInterface $pageType)
    {
        /** @var PageInterface[] $pages */
        $pages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findBy([
            'pageType' => $pageType,
        ]);

        if (count($pages) === 0) {
            return false;
        }

        foreach ($pages as $page) {
            $page->setPageType(null);
        }

        return true;
    }

    /**
     * Get page meta by its id
     *
     * @param int $id
     *
     * @return null|PageMetaInterface
     */
    public function getPageMetaById($id)
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('page_meta'))->find($id);
    }
}
