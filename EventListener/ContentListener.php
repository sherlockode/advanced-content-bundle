<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentListener
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ContentTypeManager  $contentTypeManager
     * @param TranslatorInterface $translator
     */
    public function __construct(ContentTypeManager $contentTypeManager, TranslatorInterface $translator)
    {
        $this->contentTypeManager = $contentTypeManager;
        $this->translator = $translator;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof ContentInterface) {
            return;
        }

        if (!$this->contentTypeManager->canCreateContent($object->getContentType())) {
            throw new AccessDeniedException(
                $this->translator->trans('content_type.errors.unique_content', [], 'AdvancedContentBundle')
            );
        }
    }
}
