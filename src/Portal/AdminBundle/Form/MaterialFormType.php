<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\Material;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\StructureTemplate;
use Portal\ContentBundle\Entity\Menu;

class MaterialFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
            ->add('file', VichFileType::class, [
                'label' => false,
                'allow_delete' => false,
                'required' => false
            ])
            ->add('menuNode', EntityType::class, [
                'label' => 'article_form.bloc',
                'class' => 'PortalContentBundle:MenuNode',
                'choice_label' => 'title',
                'em' => 'customer',
                'placeholder' => 'placeholder_select',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m_n')
                        ->join('m_n.menu', 'm')
                        ->join('m_n.structureTemplate', 'st')
                        ->where('m.code = :code')
                        ->andWhere('st.code = :codeTpl')
                        ->andWhere('m_n.isDeleted = false')
                        ->andWhere('m_n.isPublished = true')
                        ->setParameters([
                            'code' => Menu::STRUCTURE_MENU,
                            'codeTpl' => StructureTemplate::MATERIAL
                        ]);
                },
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'validation_groups',
        ]);
    }
}