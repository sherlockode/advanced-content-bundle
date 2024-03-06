<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\MimeTypeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    /**
     * @var MimeTypeManager
     */
    private MimeTypeManager $mimeTypeManager;

    /**
     * @param MimeTypeManager $mimeTypeManager
     */
    public function __construct(MimeTypeManager $mimeTypeManager)
    {
        $this->mimeTypeManager = $mimeTypeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('title');
        $builder
            ->add('alt', TextType::class, [
                'label' => 'field_type.image.alt',
                'required' => false,
            ])
            ->add('link', UrlType::class, [
                'label' => 'field_type.image.link',
                'required' => false,
            ])
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
            'mime_types' => array_flip(array_map('ucfirst', $this->mimeTypeManager->getImageMimeTypesChoices())),
            'mime_types_constraint' => $this->mimeTypeManager->getMimeTypesByCode(MimeTypeManager::MIME_TYPE_IMAGE),
        ]);
    }
}
