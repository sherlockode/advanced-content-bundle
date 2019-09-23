<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\LayoutToIdTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlexibleGroupType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ConfigurationManager $configurationManager
     * @param ObjectManager        $om
     */
    public function __construct(ConfigurationManager $configurationManager, ObjectManager $om)
    {
        $this->configurationManager = $configurationManager;
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $layoutRepository = $this->om->getRepository($this->configurationManager->getEntityClass('layout'));
        $builder->add('layout', HiddenType::class);
        $builder->get('layout')->addViewTransformer(new LayoutToIdTransformer($layoutRepository));

        $builder->add('position', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!$data) {
                return;
            }
            $form->add('children', RepeaterFieldType::class, [
                'label' => false,
                'fields' => $data->getLayout() ? $data->getLayout()->getChildren() : [],
                'contentType' => $options['contentType'],
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options, $layoutRepository) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!$data || !isset($data['layout'])) {
                return;
            }
            $layoutId = $data['layout'];
            $layout = $layoutRepository->find($layoutId);
            if ($layout) {
                $form->add('children', RepeaterFieldType::class, [
                    'label' => false,
                    'fields' => $layout->getChildren(),
                    'contentType' => $options['contentType'],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->configurationManager->getEntityClass('field_group_value'),
            'label' => false,
            'parentFormId' => null,
        ]);
        $resolver->setRequired(['contentType']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['parentFormId'] = $options['parentFormId'];
    }

    public function getBlockPrefix()
    {
        return 'acb_flexible_group';
    }
}
