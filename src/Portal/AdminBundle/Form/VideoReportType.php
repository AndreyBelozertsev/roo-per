<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\VideoReport;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\StructureTemplate;

class VideoReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
                'required' => false
            ])
            ->add('menuNode', EntityType::class, [
                'label' => 'document_form.bloc',
                'class' => 'PortalContentBundle:MenuNode',
                'choice_label' => 'title',
                'placeholder' => 'placeholder_select',
                'em' => 'customer',
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
                            'codeTpl' => StructureTemplate::VIDEO_REPORT
                        ]);
                },
                'required' => false,
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'document_form.pub_date',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('attachment', VideoReportAttachmentFormType::class, [
                'label' => 'video_report_form.file'
            ])
//            ->add('tags', EntityType::class, [
//                'class' => Tag::class,
//                'choice_label' => 'tag',
//                'label' => 'tags',
//                'em' => 'customer',
//                'multiple' => true,
//                'required' => false
//            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VideoReport::class,
            'translation_domain' => 'messages'
        ]);

        $resolver->setDefined([
            'validation_groups'
        ]);
    }
}
