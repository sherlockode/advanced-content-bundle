<?php

namespace Sherlockode\AdvancedContentBundle\Slug;

interface SlugProviderInterface
{
    /**
     * @param string $slug
     * @param string $className
     * @param string $fieldName
     * @param array  $additionalCriteria
     *
     * @return string
     */
    public function getValidSlug(string $slug, string $className, string $fieldName, array $additionalCriteria = []): string;
}
