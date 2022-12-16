<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Portal\AdminBundle\Entity\Instance;

class InstanceCategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title', TextType::class, array(
                    'label' => 'instance_category_form.title',
                    'required' => false,
                ))
                ->add('save', SubmitType::class, array(
                    'label' => 'instance_category_form.save'
                ))
                ;
    }

    public function getName()
    {
        return 'InstanceCategoryFormType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'newInstanceCategory' => false,
        ));
        $resolver->setDefined(array(
            'newInstanceCategory', 
        ));
    }

}
