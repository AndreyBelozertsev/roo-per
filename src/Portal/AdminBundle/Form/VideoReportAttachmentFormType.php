<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Portal\ContentBundle\Entity\VideoReportAttachment;
use Vich\UploaderBundle\Form\Type\VichFileType;

class VideoReportAttachmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'label' => false,
                'attr' => ['accept' => 'video/mpeg,video/mp4,video/ogg,video/webm,video/x-flv'],
                'required' => false,
                'allow_delete' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VideoReportAttachment::class
        ]);
    }
}
