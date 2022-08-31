<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\SerializedStringToEntities;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToEntity;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractEntity extends AbstractFieldType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return EntityType::class;
    }

    /**
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['is_multiple'];
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'choice';
    }

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
        $builder->get('options')
            ->add('is_multiple', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ]
            ])
        ;

        parent::addFieldOptions($builder);
    }

    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        $fieldOptions = $this->getFieldOptions($field);

        $isMultiple = false;
        if (isset($fieldOptions['is_multiple']) && $fieldOptions['is_multiple']) {
            $isMultiple = true;
        }

        $formFieldOptions = [];
        $formFieldOptions['class'] = $this->getEntityClass();
        $formFieldOptions['choice_label'] = $this->getEntityChoiceLabel();
        $formFieldOptions['multiple'] = $isMultiple;
        $formFieldOptions['required'] = $field->isRequired();

        return $formFieldOptions;
    }

    /**
     * Get model transformer for value field
     *
     * @param FieldInterface $field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer()
    {
        if ($this->getIsMultipleChoice($field)) {
            return new SerializedStringToEntities($this->em, $this->getEntityClass(), $this->getUniqueFieldIdentifier());
        }

        return new StringToEntity($this->em, $this->getEntityClass(), $this->getUniqueFieldIdentifier());
    }

    /**
     * Check if field accept several choices
     *
     * @param FieldInterface $field
     *
     * @return bool
     */
    public function getIsMultipleChoice(FieldInterface $field)
    {
        $fieldOptions = $this->getFieldOptions($field);
        if (isset($fieldOptions['is_multiple'])) {
            return $fieldOptions['is_multiple'];
        }

        return false;
    }

    /**
     * Get entity unique identifier
     *
     * @return string
     */
    protected function getUniqueFieldIdentifier()
    {
        return 'id';
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return array
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        $fieldValueValue = $fieldValue->getValue();
        if (!$this->getIsMultipleChoice($fieldValue->getField())) {
            return [
                'value' => $fieldValueValue,
                'entity' => $this->getEntityByIdentifier($fieldValueValue),
            ];
        }

        $entities = [];
        foreach (unserialize($fieldValueValue) as $entityValue) {
            $entity = $this->getEntityByIdentifier($entityValue);
            if ($entity !== null) {
                $entities[] = [
                    'value' => $entityValue,
                    'entity' => $entity
                ];
            }
        }

        return $entities;
    }

    /**
     * @param mixed $entityValue
     *
     * @return null|object
     */
    public function getEntityByIdentifier($entityValue)
    {
        return $this->em->getRepository($this->getEntityClass())->findOneBy([
            $this->getUniqueFieldIdentifier() => $entityValue,
        ]);
    }

    /**
     * Get entity class
     *
     * @return string
     */
    abstract protected function getEntityClass();

    /**
     * Get entity choice label
     *
     * @return string
     */
    abstract protected function getEntityChoiceLabel();
}
