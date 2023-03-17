<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
//            ->add('birthdate', DateType::class, [
//                'label' => 'Date de naissance',
//                'required' => true,
//                'widget' => 'single_text',
//            ])
//            ->add('gender', ChoiceType::class, [
//                'label' => 'Genre',
//                'required' => true,
//                'choices' => [
//                    'Homme' => 'male',
//                    'Femme' => 'female',
//                ],
//            ])
//            ->add('city', TextType::class, [
//                'label' => 'Ville de résidence',
//                'required' => true,
//            ])
//            ->add('address', TextType::class, [
//                'label' => 'Adresse',
//                'required' => true,
//            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
            ])
            ->add('phoneNumber', IntegerType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
//            ->add('social_media', TextType::class, [
//                'label' => 'Adresse de réseaux sociaux',
//                'required' => true,
//            ])
//            ->add('language', ChoiceType::class, [
//                'label' => 'Langue',
//                'required' => true,
//                'choices' => [
//                    'Français' => 'fr',
//                    'Anglais' => 'en',
//                    'Espagnol' => 'es',
//                    'Italien' => 'it',
//                    'Allemand' => 'de',
//                    'Arabe' => 'ar',
//                ],
//            ])
//            ->add('language_level', ChoiceType::class, [
//                'label' => 'Niveau de langue',
//                'required' => true,
//                'choices' => [
//                    'Débutant (A1)' => 'A1-beginner',
//                    'Débutant (A2)' => 'A2-beginner',
//                    'Notions' => 'basic',
//                    'Intermédiaire (B1)' => 'B1-intermediate',
//                    'Intermédiaire avancé (B2)' => 'B2-upper-intermediate',
//                    'Opérationnel (C1)' => 'C1-advanced',
//                    'Avancé (C2)' => 'C2-proficient',
//                    'Bilingue' => 'bilingual',
//                    'Langue maternelle' => 'native',
//                ],
        ;
    }
}

