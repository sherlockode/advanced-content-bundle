<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AcbFileType extends AbstractType
{
    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @param UploadManager $uploadManager
     */
    public function __construct(UploadManager $uploadManager)
    {
        $this->uploadManager = $uploadManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'field_type.file.title',
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($options) {
                $form = $event->getForm();
                $data = $event->getData();
                if (!is_array($data)) {
                    $data = [];
                }

                $this->updateForm($form, $data, $options);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $data = $event->getData();
                $form = $event->getForm();
                if (!empty($data['delete'])) {
                    if (!empty($data['src'])) {
                        $this->uploadManager->remove($data['src']);
                        unset($data['src']);
                    }
                    unset($data['delete']);
                }

                if (!empty($data['file'])) {
                    $data['src'] = $this->uploadManager->upload($data['file']);
                    $this->updateForm($form, $data, $options);
                    unset($data['file']);
                } elseif (isset($data['src'])) {
                    $this->updateForm($form, $data, $options);
                }

                $event->setData($data);
            }
        );
    }

    /**
     * @param FormInterface $form
     * @param array         $data
     * @param array         $options
     *
     * @return void
     */
    public function updateForm(FormInterface $form, $data, $options)
    {
        if (!isset($data['src'])) {
            $data['src'] = '';
        }
        $isFileUploaded = $this->uploadManager->isFileUploaded($data['src']);
        $form
            ->add('file', FileType::class, array_merge([
                'label' => 'field_type.file.file',
                'required' => !$isFileUploaded,
            ], $isFileUploaded ? [] : [
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ]))
        ;

        if ($isFileUploaded) {
            $form
                ->add('delete', CheckboxType::class, [
                    'label' => 'field_type.file.delete',
                    'required' => false,
                ]);
            $form->add('src', HiddenType::class);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }

    /**
     * Make the image accessible in view
     *
     * @param FormView      $view    The view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['uploadManager'] = $this->uploadManager;
        $view->vars['src'] = $form->getData()['src'] ?? '';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_file';
    }
}
