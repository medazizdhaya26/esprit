<?php

namespace App\Form;

use App\Entity\Entraineur;
use App\Entity\SalleDeSport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EntraineurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom']
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le prénom']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'email est obligatoire"]),
                    new Assert\Email(['message' => 'Adresse email invalide']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{8,11}$/',
                        'message' => 'Le numéro de téléphone se compose de 8 à 11 chiffres.'
                    ])
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le numéro de téléphone']
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'adresse est obligatoire"]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => "Entrez l'adresse"]
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => "La date de naissance est obligatoire"]),
                    new Assert\LessThan([
                        'value' => 'today',
                        'message' => "La date de naissance doit être dans le passé."
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('qualifications', ChoiceType::class, [
                'label' => 'Qualifications',
                'choices' => [
                    'Certifications sportives' => 'certifications_sportives',
                    'Formation en psychologie du sport' => 'psychologie_sport',
                    'Formation en premiers secours' => 'secours',
                ],
                'expanded' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control rounded-3'],
            ])

           ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez une description'],
                'constraints' => [
                    new Assert\Length(['max' => 1000, 'maxMessage' => 'La description ne peut pas dépasser 1000 caractères.'])
                ],
            ])
            ->add('specialite', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "La specialité est obligatoire"]),
                    new Assert\Length(['max' => 255]),
                ]
    ])
                ->add('specialite', ChoiceType::class, [
                    'label' => 'Spécialité',
                    'choices' => [
                        'Coaching de zumba' => 'zumba', 
                        'Coaching de football' => 'football',
                        'Coaching de basketball' => 'basketball',
                        'Coaching de fitness' => 'fitness',
                        'Coaching de natation' => 'natation',  
                        'Coaching de tennis' => 'tennis',      
                        'Coaching de yoga' => 'yoga',          
                        'Coaching de dance' => 'dance', 
                        'Coaching de boxe' => 'boxe', 
                        'Autre' => 'autre',
                    ],
                    'expanded' => false,
                    'multiple' => false,
                    'attr' => ['class' => 'form-control rounded-3'],
               
            ])

            ->add('salle', EntityType::class, [
                'class' => SalleDeSport::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une salle',
                'required' => false,
                'label' => 'Salle de sport',
                'attr' => ['class' => 'form-control rounded-3'],
            ])
           
            ->add('image', FileType::class, [
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entraineur::class,
        ]);
    }
}
