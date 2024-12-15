<?php

namespace App\Form;

use App\Entity\Bibliothecaire;
use App\Entity\Bibliotheque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Component\Validator\Constraints\Regex;

class BibliothecaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Length(['min' => 3, 'max' => 100])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length(['min' => 3, 'max' => 100])
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['maxlength' => 11],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Regex([
                        'pattern' => '/^\d{8,11}$/',
                        'message' => 'Le numéro de téléphone doit contenir uniquement des chiffres et être composé de 8 à 11 caractères.',
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text'
            ])
            ->add('specialisation', ChoiceType::class, [
                'label' => 'Spécialisation',
                'choices' => [
                    'Littérature' => 'litterature',
                    'Sciences' => 'sciences',
                    'Histoire' => 'histoire',
                    'Autre' => 'autre',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control rounded-3'],
            ])
            ->add('competences', ChoiceType::class, [
                'label' => 'Compétences',
                'choices' => [
                    'Gestion des archives' => 'archives',
                    'Maîtrise des logiciels de bibliothèque' => 'logiciels_bibliotheque',
                    'Organisation d\'événements littéraires' => 'evenements_litteraires',
                ],
                'expanded' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control rounded-3'],
            ])
            ->add('bibliotheque', EntityType::class, [
                'class' => Bibliotheque::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une bibliothèque',
                'required' => false,
                'label' => 'Bibliothèque',
                'attr' => ['class' => 'form-control rounded-3'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (fichier PNG, JPG ou JPEG)',
                'mapped' => false, // Ne lie pas directement le champ image à l'entité
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG)',
                        'maxSize' => '2M',  // Limite la taille du fichier à 2 Mo
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 2 Mo',
                    ])
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bibliothecaire::class,
        ]);
    }
}
