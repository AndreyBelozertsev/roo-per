<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\PhotoReport;
use Portal\ContentBundle\Entity\StructureTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\Event;
use Portal\ContentBundle\Entity\Tag;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'subtitle',
                'required' => false
            ])
            ->add('startDate', DateType::class, [
                'label' => 'date_start',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('endDate', DateType::class, [
                'label' => 'date_end',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('content', TextareaType::class, [
                'label' => 'content',
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
                            'codeTpl' => StructureTemplate::EVENT
                        ]);
                },
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'publish_date',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('isImportant', CheckboxType::class, [
                'label' => 'event.important',
                'required' => false
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'tag',
                'label' => 'tags',
                'em' => 'customer',
                'multiple' => true,
                'required' => false
            ])
            ->add('attachment', EventAttachmentFormType::class, [
                'label' => 'event.preview'
            ])
            ->add('place', TextType::class, [
                'label' => 'event.place',
                'required' => false
            ])
            ->add('location', HiddenType::class, [
                'label' => 'template.interactive_map'
            ])
            ->add('photoReport', EntityType::class, [
                'class' => PhotoReport::class,
                'em' => 'customer',
                'label' => 'article_form.photo_report',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ]);

        if (isset($options['slug']) && $options['slug']) {
            $builder->add('slug', TextType::class, [
                'label' => 'document_form.slug',
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'translation_domain' => 'messages'
        ]);

        $resolver->setDefined([
            'slug',
            'validation_groups'
        ]);
    }
}
