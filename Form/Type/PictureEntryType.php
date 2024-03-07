<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class PictureEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('alt');
        $builder->remove('mime_type');
        $builder->remove('link');
        $builder
            ->add('media_query', TextType::class, [
                'label' => 'field_type.image.media_query',
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $data = $event->getData();
                if (isset($data['mime_type'])) {
                    // Remove mime type data to prevent form extra fields error
                    unset($data['mime_type']);
                }
                $event->setData($data);
            },
            1
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ImageType::class;
    }
}
