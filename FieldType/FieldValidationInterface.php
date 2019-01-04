<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

interface FieldValidationInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function validate($data);
}
