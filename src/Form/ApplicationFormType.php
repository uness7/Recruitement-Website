<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Candidate;
use App\Entity\JobListing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('applicantName', TextType::class)
            ->add('applicantEmail', EmailType::class)
            ->add('applicantPhoneNumber', TextType::class)
            ->add('resume', FileType::class, ['data_class' => null])
            ->add('coverLetter', FileType::class, ['data_class' => null,])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}