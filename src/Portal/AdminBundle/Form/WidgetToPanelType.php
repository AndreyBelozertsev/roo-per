<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Widget;
use Portal\ContentBundle\Entity\WidgetPanel;
use Portal\ContentBundle\Entity\StructureTemplate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\WidgetToPanel;

class WidgetToPanelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'widget_to_panel_form.label',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-1 control-label'],
            ])
            ->add('widgetId', EntityType::class, [
                'label' => 'widget_to_panel_form.widget_id',
                'required' => false,
                'placeholder' => false,
                'class' => Widget::class,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'em' => 'customer',
            ])
            ->add('panelId', EntityType::class, [
                'label' => 'widget_to_panel_form.panel_id',
                'required' => false,
                'placeholder' => false,
                'class' => WidgetPanel::class,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'em' => 'customer',
            ])
            ->add('structureTemplate', EntityType::class, [
                'label' => 'widget_to_panel_form.template_id',
                'required' => false,
                'placeholder' => false,
                'class' => StructureTemplate::class,
                'attr' => ['class' => 'form-control'],
                'multiple' => true,
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'em' => 'customer',
            ])
            ->add('widgetOrder', TextType::class, [
                'label' => 'widget_to_panel_form.widget_order',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-1 control-label'],
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'widget_to_panel_form.is_published',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WidgetToPanel::class,
            'translation_domain' => 'messages',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_widget_to_panel';
    }
}
