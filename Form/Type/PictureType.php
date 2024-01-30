<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', ImageType::class, [
                'label' => 'field_type.image.picture_main',
                'row_attr' => ['class' => 'picture-main-image'],
            ])
            ->add('sources', CollectionType::class, [
                'label' => 'field_type.image.picture_sources',
                'entry_type' => PictureEntryType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'row_attr' => ['class' => 'picture-sources'],
                'entry_options' => [
                    'attr' => ['class' => 'picture-source'],
                    'label' => false,
                ],
            ]);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $data = $event->getData();
                $globalMimeTypes = $data['image']['mime_type'] ?? [];
                if (isset($data['sources']) && is_array($data['sources'])) {
                    foreach ($data['sources'] as $key => $source) {
                        $data['sources'][$key]['mime_type'] = $globalMimeTypes;
                    }
                }

                $event->setData($data);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'row_attr' => ['class' => 'picture-field'],
        ]);
    }
}
