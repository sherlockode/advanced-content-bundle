<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractImport
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $entityClasses = [];

    /**
     * @var bool
     */
    protected $allowUpdate = true;

    /**
     * @var Slugify
     */
    protected $slugify;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->init();
    }

    protected function init()
    {
        $this->entityClasses = $this->configurationManager->getEntityClasses();
        $this->slugify = new Slugify();
    }

    /**
     * @param bool $allowUpdate
     *
     * @return $this
     */
    public function setAllowUpdate($allowUpdate)
    {
        $this->allowUpdate = $allowUpdate;

        return $this;
    }

    /**
     * @param string $slug
     * @param array  $data
     *
     * @return ImportResult
     */
    public function importData($slug, $data)
    {
        $result = new ImportResult();
        $result->success();

        try {
            $this->resetErrors();

            $this->importEntity($slug, $data);
            foreach ($this->errors as $error) {
                $result->addMessage($error);
            }
            if (count($this->errors) > 0) {
                $result->failure();
            }
        } catch (\Exception $e) {
            $result
                ->failure()
                ->addMessage($e->getMessage())
            ;
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function resetErrors()
    {
        $this->errors = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $slug
     * @param array  $data
     *
     * @return void
     */
    abstract protected function importEntity($slug, $data);
}
