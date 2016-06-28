<?php

namespace AppBundle\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,['label'=>false,'attr'=>['placeholder'=>"Name"]])
            ->add('email',EmailType::class,['label'=>false,'attr'=>['placeholder'=>"Email"]])
            ->add('subject',TextType::class,['label'=>false,'attr'=>['placeholder'=>"Subject"]])
            ->add('text',TextareaType::class,['label'=>false,'attr'=>['placeholder'=>"Message"]])
            ->add('Send',SubmitType::class,['label'=>false,'attr'=>['class'=>'btn btn-primary']])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' =>   NULL,
            'method'     =>  'Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_bundle_contact_type';
    }
}