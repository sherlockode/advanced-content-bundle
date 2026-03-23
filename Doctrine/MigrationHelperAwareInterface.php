<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Doctrine;

interface MigrationHelperAwareInterface
{
    /**
     * @return $this
     */
    public function setHelper(MigrationHelperInterface $helper): self;
}
