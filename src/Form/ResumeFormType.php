<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ResumeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('birthdate', DateType::class, [
                'label' => 'Birthdate',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender',
                'required' => true,
                'choices' => [
                    'Man' => 'male',
                    'Woman' => 'female',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('phoneNumber', IntegerType::class, [
                'label' => 'Phone Number',
                'required' => true,
            ])
            ->add('social_media', TextType::class, [
                'label' => 'Social Media',
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Language',
//                'required' => true,
                'choices' => [
                    'French' => 'fr',
                    'English' => 'en',
                    'Arabic' => 'ar',
                ],
            ])
            ->add('language_level', ChoiceType::class, [
                'label' => 'language level',
//                'required' => true,
                'choices' => [
                    'Beginner' => 'beginner',
                    'Intermediate' => 'intermediate',
                    'Advanced' => 'advanced'
                ]]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(
            [
                'data_class' => null
            ]
        );
    }
}

