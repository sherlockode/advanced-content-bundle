<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Sherlockode\AdvancedContentBundle\Event\AcbFilePostValidate;
use Sherlockode\AdvancedContentBundle\Event\AcbFilePreSubmitEvent;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AcbFileListener implements EventSubscriberInterface
{
    private array $files = [];

    public function __construct(private readonly UploadManager $uploadManager)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AcbFilePreSubmitEvent::NAME => 'onPreSubmit',
            AcbFilePostValidate::NAME => 'onPostValidate',
        ];
    }

    public function onPreSubmit(AcbFilePreSubmitEvent $event): void
    {
        $this->files = array_merge($this->files, [['file' => $event->getUploadedFile(), 'fileName' => $event->getFileName()]]);
    }

    public function onPostValidate(): void
    {
        foreach ($this->files as $file) {
            if (null !== $file['file'] ?? null && null !== $file['fileName'] ?? null) {
                $this->uploadManager->upload($file['file'], $file['fileName']);
            }
        }
    }
}
