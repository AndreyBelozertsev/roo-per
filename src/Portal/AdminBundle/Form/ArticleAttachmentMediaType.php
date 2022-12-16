<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\ArticleMediaAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ArticleAttachmentMediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'label' => false,
                'attr' => [
                    'accept' => 'video/mp4,video/mkv,video/avi,video/mpeg,audio/mpeg,audio/x-mpeg-3,audio/ogg,audio/x-ms-wma,audio/aac,audio/ac3,audio/dts,audio/flac',
                    'class' => 'load-img-button'
                ],
                'required' => false,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleMediaAttachment::class,
            'translation_domain' => 'messages'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_article_attachment_media';
    }
}
