<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

class ConfigurationManager
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Set bundle configuration
     *
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Get entity class configuration for given type
     *
     * @param string $type
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getEntityClass($type)
    {
        if (!isset($this->config['entity_class'][$type])) {
            throw new \RuntimeException('You are trying to access a configuration that does not exist.');
        }

        return $this->config['entity_class'][$type];
    }

    /**
     * @return array
     */
    public function getEntityClasses()
    {
        return $this->config['entity_class'];
    }

    /**
     * @return mixed
     */
    public function getImageDirectory()
    {
        return $this->config['upload']['image_directory'];
    }

    /**
     * @return string
     */
    public function getInitDirectory()
    {
        return $this->config['init_command']['directory'];
    }

    /**
     * @return string
     */
    public function getInitFilesDirectory()
    {
        return $this->config['init_command']['files_directory'];
    }

    /**
     * @return bool
     */
    public function initCanUpdate()
    {
        return $this->config['init_command']['allow_update'];
    }

    /**
     * @return string
     */
    public function getDefaultWysiwygToolbar()
    {
        return $this->getDefaultOptionValue('wysiwyg_toolbar');
    }

    /**
     * @return bool
     */
    public function getDefaultDateIncludeTime()
    {
        return $this->getDefaultOptionValue('date_include_time');
    }

    /**
     * @return bool
     */
    public function isScopesEnabled()
    {
        return $this->config['scopes']['enabled'];
    }

    /**
     * @param string $option
     *
     * @return mixed
     */
    private function getDefaultOptionValue($option)
    {
        return $this->config['default_options'][$option];
    }
}
