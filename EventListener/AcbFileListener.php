<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Sherlockode\AdvancedContentBundle\Event\AcbFileEvent;
use Sherlockode\AdvancedContentBundle\Event\AcbFilePostValidate;
use Sherlockode\AdvancedContentBundle\Event\AcbFilePreSubmitEvent;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AcbFileListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $files = [];

    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @param UploadManager $uploadManager
     */
    public function __construct(UploadManager $uploadManager)
    {
        $this->uploadManager = $uploadManager;
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

    /**
     * @param AcbFilePreSubmitEvent $event
     *
     * @return void
     */
    public function onPreSubmit(AcbFilePreSubmitEvent $event): void
    {
        $this->files = array_merge($this->files, [['file' => $event->getUploadedFile(), 'fileName' => $event->getFileName()]]);

    }

    /**
     * @return void
     */
    public function onPostValidate(): void
    {
        foreach ($this->files as $file) {
            if (null !== $file['file'] ?? null && null !== $file['fileName'] ?? null) {
                $this->uploadManager->upload($file['file'], $file['fileName']);
            }
        }
    }
}
