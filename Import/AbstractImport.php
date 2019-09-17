<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractImport
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FieldManager
     */
    protected $fieldManager;

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
     * @param ObjectManager        $om
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param FieldManager         $fieldManager
     */
    public function __construct(
        ObjectManager $om,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        FieldManager $fieldManager
    ) {
        $this->om = $om;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->fieldManager = $fieldManager;
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
     * @param \SplFileInfo $file
     *
     * @return ImportResult
     */
    public function importFile(\SplFileInfo $file)
    {
        $result = new ImportResult();
        $result->success();

        try {
            $filePath = $file->getRealPath();
            $data = Yaml::parseFile($filePath);
            $this->resetErrors();

            $this->importData($data);
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
     * @param array $data
     *
     * @return void
     */
    abstract protected function importData($data);
}
