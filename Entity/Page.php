<?php

namespace Sherlockode\AdvancedContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\Page as BasePage;

/**
 * @ORM\Entity
 * @ORM\Table(name="acb_page")
 */
class Page extends BasePage
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Sherlockode\AdvancedContentBundle\Entity\Content", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $contents;

    /**
     * @ORM\ManyToOne(targetEntity="Sherlockode\AdvancedContentBundle\Entity\PageType")
     * @ORM\JoinColumn(name="page_type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $pageType;

    /**
     * @ORM\OneToMany(targetEntity="Sherlockode\AdvancedContentBundle\Entity\PageMeta", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $pageMetas;
}
