<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Head;
use Portal\ContentBundle\Entity\Menu;
//use Portal\ContentBundle\Entity\Tag;
use Portal\ContentBundle\Entity\StructureTemplate;
use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\PhotoReport;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class HeadFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'head_form.firstname',
                'required' => false
            ])
            ->add('middlename', TextType::class, [
                'label' => 'head_form.middlename',
                'required' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => 'head_form.lastname',
                'required' => false
            ])
            ->add('position', TextType::class, [
                'label' => 'head_form.position',
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => 'head_form.phone',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'head_form.address',
                'required' => false
            ])
            ->add('fax', TextType::class, [
                'label' => 'head_form.fax',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'head_form.email',
                'required' => false
            ])
//            ->add('contactInformation', TextareaType::class, [
//                'label' => 'head_form.contactInformation',
//                'required' => false
//            ])
            ->add('biography', TextareaType::class, [
                'label' => 'head_form.biography',
                'required' => false
            ])
//            ->add('education', TextareaType::class, [
//                'label' => 'head_form.education',
//                'required' => false
//            ])
//            ->add('activity', TextareaType::class, [
//                'label' => 'head_form.activity',
//                'required' => false
//            ])
//            ->add('awards', TextareaType::class, [
//                'label' => 'head_form.awards',
//                'required' => false
//            ])
            ->add('menuNode', EntityType::class, [
                'label' => 'head_form.bloc',
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
                            'codeTpl' => StructureTemplate::HEAD
                        ]);
                },
                'required' => false
            ])
            ->add('attachment', HeadAttachmentFormType::class, [
                'label' => false
            ])
//            ->add('publishedAt', DateType::class, [
//                'label' => 'head_form.published_date',
//                'required' => false,
//                'widget' => 'single_text',
//                'data' => new \DateTime(),
//            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false,
            ])
        ;

//        if (isset($options['hasGranted']) && $options['hasGranted']) {
//            $builder->add('manualViewsCounter', TextType::class, [
//                'label' => 'head_form.counter',
//                'required' => false
//            ]);
//        }
        if (isset($options['slug']) && $options['slug']) {
            $builder
                ->add('slug', TextType::class, [
                'label' => 'head_form.slug',
                'required' => false
                ])
                ->add('isLinkOnId', CheckboxType::class, [
                    'label' => 'menu_page.is_link_on_id',
                    'required' => false
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Head::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'instanceList',
            'instanceCode',
//            'hasGranted',
            'slug',
            'validation_groups',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_head';
    }
}
