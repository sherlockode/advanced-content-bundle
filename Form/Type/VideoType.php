<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'label' => 'field_type.video.url',
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('muted', CheckboxType::class, [
                'label' => 'field_type.video.muted',
                'required' => false,
            ])
            ->add('autoplay', CheckboxType::class, [
                'label' => 'field_type.video.autoplay',
                'required' => false,
            ])
            ->add('loop', CheckboxType::class, [
                'label' => 'field_type.video.loop',
                'required' => false,
            ])
            ->add('controls', CheckboxType::class, [
                'label' => 'field_type.video.controls',
                'required' => false,
            ])
            ->add('height', NumberType::class, [
                'label' => 'field_type.video.height',
                'required' => false,
            ])
            ->add('width', NumberType::class, [
                'label' => 'field_type.video.width',
                'required' => false,
            ])
        ;
    }
}
