<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\SystemMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SystemModeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'system_mode_form.code',
                'required' => false,
            ])
            ->add('notificationMessage', TextType::class, [
                'label' => 'system_mode_form.notification_message',
                'required' => false,
            ])
            ->add('modeMessage', TextType::class, [
                'label' => 'system_mode_form.mode_message',
                'required' => false,
            ])
            ->add('isActiveNotification', CheckboxType::class, [
                'label' => 'system_mode_form.is_active_notification',
                'required' => false,
            ])
            ->add('isActiveMode', CheckboxType::class, [
                'label' => 'system_mode_form.is_active_mode',
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
            'data_class' => SystemMode::class,
            'translation_domain' => 'messages',
        ]);
        $resolver->setDefined([
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'system_mode_form';
    }
}
