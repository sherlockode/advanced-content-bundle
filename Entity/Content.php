<?php

namespace Sherlockode\AdvancedContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sherlockode\AdvancedContentBundle\Model\Content as BaseContent;

/**
 * @ORM\Entity
 * @ORM\Table(name="acb_content")
 */
class Content extends BaseContent
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
     * @ORM\ManyToOne(targetEntity="Sherlockode\AdvancedContentBundle\Entity\Page", inversedBy="contents")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $page;
}
