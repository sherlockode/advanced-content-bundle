<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;

class ImageCarouselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', RepeaterType::class, [
                'entry_type' => ImageType::class,
                'label' => 'field_type.image_carousel.images',
                'constraints' => [
                    new Count(null, 1, null, null, null, null, null, null, $options['validation_groups']),
                ],
                'error_bubbling' => false,
            ])
            ->add('displayArrows', CheckboxType::class, [
                'label' => 'field_type.image_carousel.display_arrows',
                'required' => false,
            ])
            ->add('displayPagination', CheckboxType::class, [
                'label' => 'field_type.image_carousel.display_pagination',
                'required' => false,
            ])
        ;
    }
}
