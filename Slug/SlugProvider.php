<?php

namespace Sherlockode\AdvancedContentBundle\Slug;

use Doctrine\ORM\EntityManagerInterface;

class SlugProvider implements SlugProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $slug
     * @param string $className
     * @param string $fieldName
     * @param array  $additionalCriteria
     *
     * @return string
     */
    public function getValidSlug(string $slug, string $className, string $fieldName, array $additionalCriteria = []): string
    {
        do {
            $existingEntity = $this->em->getRepository($className)->findOneBy(array_merge([
                $fieldName => $slug,
            ], $additionalCriteria));
            if ($existingEntity !== null) {
                if (preg_match('/-(\d+)$/', $slug, $matches) && array_key_exists(1, $matches)) {
                    $slug = preg_replace('/' . $matches[1] . '$/', $matches[1] + 1, $slug);
                } else {
                    $slug .= '-1';
                }
            }
        } while ($existingEntity !== null);

        return $slug;
    }
}
