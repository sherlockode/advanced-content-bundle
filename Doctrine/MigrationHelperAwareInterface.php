<?php

namespace Sherlockode\AdvancedContentBundle\Doctrine;

interface MigrationHelperAwareInterface
{
    /**
     * @param MigrationHelperInterface $helper
     *
     * @return $this
     */
    public function setHelper(MigrationHelperInterface $helper): self;
}
