<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['label' => 'field_type.file.file'])
            ->add('src', HiddenType::class)
            ->add('delete', CheckboxType::class, ['required' => false, 'label' => 'field_type.file.delete'])
            ->add('title', TextType::class, ['label' => 'field_type.file.title'])
        ;

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($options) {
                $data = $event->getData();
                $data['src'] = $options['uploadManager']->upload($data['file']);
                unset($data['file']);
                $event->setData($data);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', 'AdvancedContentBundle');
        $resolver->setRequired(['uploadManager']);
    }

    /**
     * Make message accessible in view
     *
     * @param FormView      $view    The view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['uploadManager'] = $options['uploadManager'];
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_file';
    }
}
