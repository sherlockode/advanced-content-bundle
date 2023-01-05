<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageCarouselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', RepeaterType::class, [
                'entry_type' => ImageType::class,
                'label' => 'field_type.image_carousel.images',
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
