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
            ->add('title', TextType::class, ['label' => 'field_type.file.title'])
        ;

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                if (!is_array($data)) {
                    $data = [];
                }

                $this->updateForm($form, $data);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if ($data['file']) {
                    $data['src'] = $this->uploadManager->upload($data['file']);
                    $this->updateForm($form, $data);
                    unset($data['file']);
                    $event->setData($data);
                } elseif (isset($data['src'])) {
                    $this->updateForm($form, $data);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!empty($data['delete'])) {
                    unset($data['src']);
                    unset($data['delete']);
                }
                $event->setData($data);
            }
        );
    }

    /**
     * @param FormInterface $form
     * @param array         $data
     *
     * @return void
     */
    public function updateForm(FormInterface $form, $data)
    {
        if (!isset($data['src'])) {
            $data['src'] = '';
        }
        $isFileUploaded = $this->uploadManager->isFileUploaded($data['src']);
        $form
            ->add('file', FileType::class, [
                'label' => 'field_type.file.file',
                'required' => !$isFileUploaded,
            ])
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
