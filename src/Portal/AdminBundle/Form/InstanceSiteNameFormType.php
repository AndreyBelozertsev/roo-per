<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Portal\AdminBundle\Entity\Instance;

class InstanceSiteNameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteName', TextType::class, array(
                'label' => 'site_name_form.site_name',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'instance_form.save'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Instance::class,
            'translation_domain' => 'messages'
        ));
        $resolver->setDefined(array(
            'validation_groups',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_instance_site_name';
    }

}
