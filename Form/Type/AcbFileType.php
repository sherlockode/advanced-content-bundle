<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Event\AcbFilePreSubmitEvent;
use Sherlockode\AdvancedContentBundle\Manager\MimeTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AcbFileType extends AbstractType
{
    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MimeTypeManager
     */
    private $mimeTypeManager;

    /**
     * @param UploadManager            $uploadManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param MimeTypeManager          $mimeTypeManager
     */
    public function __construct(UploadManager $uploadManager, EventDispatcherInterface $eventDispatcher, MimeTypeManager $mimeTypeManager)
    {
        $this->uploadManager = $uploadManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mimeTypeManager = $mimeTypeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mimeTypeChoices = $options['mime_types'] ?? $this->mimeTypeManager->generateMimeTypeChoices();

        $builder
            ->add('title', TextType::class, [
                'label' => 'field_type.file.title',
                'constraints' => !$options['required'] ? [] : [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('mime_type', ChoiceType::class, [
                'label' => 'field_type.file.restriction_type',
                'multiple' => true,
                'choices' => is_array($mimeTypeChoices) ? $mimeTypeChoices : [],
                'choice_attr' => function ($choice): array {
                    return ['data-mime-type' => json_encode($this->mimeTypeManager->getMimeTypesByCode($choice))];
                },
                'attr' => ['data-mime-type-restriction' => '']
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
                    $fileName = $this->uploadManager->getFileName($data['file']);
                    $this->eventDispatcher->dispatch(
                        new AcbFilePreSubmitEvent($data['file'], $fileName),
                        AcbFilePreSubmitEvent::NAME
                    );

                    $data['src'] = $fileName;
                    $this->updateForm($form, $data, $options, true);
                } elseif (isset($data['src'])) {
                    $this->updateForm($form, $data, $options);
                }

                $event->setData($data);
            }
        );
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if ($form->getConfig()->getCompound()) {
                $cleanData = [];
                // clean data to prevent saving empty fields
                foreach ($form as $key => $child) {
                    if (isset($data[$key])) {
                        $cleanData[$key] = $data[$key];
                    }
                }

                $event->setData($cleanData);
            }
        });
    }

    /**
     * @param FormInterface $form
     * @param array         $data
     * @param array         $options
     * @param bool          $hasFile
     *
     * @return void
     */
    private function updateForm(FormInterface $form, $data, $options, $hasFile = false)
    {
        if (!isset($data['src'])) {
            $data['src'] = '';
        }

        $isFileUploaded = $this->uploadManager->isFileUploaded($data['src']) || $hasFile;

        $hasNotBlankConstraint = false;
        foreach ($options['file_constraints'] as $constraint) {
            if ($constraint instanceof NotBlank) {
                $hasNotBlankConstraint = true;
                break;
            }
        }

        if (false === $hasNotBlankConstraint && true === $options['required'] && false === $isFileUploaded) {
            $options['file_constraints'][] = new NotBlank(null, null, null, null, $options['validation_groups']);
        }

        $mimeTypes = $options['mime_types_constraint'];
        if (!empty($data['mime_type'])) {
            $mimeTypes = [];

            foreach ($data['mime_type'] as $type) {
                $mimeTypes[] = $this->mimeTypeManager->getMimeTypesByCode($type);
            }

            $mimeTypes = array_merge([], ...$mimeTypes);
        }
        $options['file_constraints'][] = new File(null, null, null, $mimeTypes);

        $form
            ->add('file', FileType::class, [
                'label' => 'field_type.file.file',
                'required' => !$isFileUploaded && $options['required'],
                'constraints' => $options['file_constraints'],
                'attr' => ['data-mime-type-restriction' => '']
            ])
        ;

        if ($isFileUploaded) {
            $form
                ->add('delete', CheckboxType::class, [
                    'label' => 'field_type.file.delete',
                    'required' => false,
                ])
                ->add('src', HiddenType::class, [
                    'required' => $options['required'],
                ])
            ;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
            'file_constraints' => [],
            'mime_types' => null,
            'mime_types_constraint' => $this->mimeTypeManager->getAllMimeTypes(),
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
        $fileSrc = $form->getData()['src'] ?? '';
        if ('' !== $fileSrc && false === $this->uploadManager->isFileUploaded($fileSrc)) {
            $fileSrc = '';
        }

        $view->vars['uploadManager'] = $this->uploadManager;
        $view->vars['src'] = $fileSrc;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_file';
    }
}
