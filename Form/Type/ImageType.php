<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('title');
        $builder
            ->add('alt', TextType::class, ['label' => 'field_type.image.alt'])
        ;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return AcbFileType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_image';
    }
}
