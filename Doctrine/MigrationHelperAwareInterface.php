<?php

namespace Sherlockode\AdvancedContentBundle\Doctrine;

interface MigrationHelperAwareInterface
{
    /**
     * @return $this
     */
    public function setHelper(MigrationHelperInterface $helper): self;
}
