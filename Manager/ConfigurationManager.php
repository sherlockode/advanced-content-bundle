<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Symfony\Component\Config\Definition\Exception\Exception;

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
     * @throws Exception
     */
    public function getEntityClass($type)
    {
        if (!isset($this->config['entity_class'][$type])) {
            throw new Exception("You are trying to access a configuration that does not exists.");
        }

        return $this->config['entity_class'][$type];
    }
}