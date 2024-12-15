<?php
namespace App\Form;

use App\Entity\SalleDeSport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SalleDeSportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la salle',
                'attr' => [
                    'placeholder' => 'Entrez le nom de la salle',
                    'class' => 'form-control',
                ],
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité',
                'attr' => [
                    'placeholder' => 'Entrez la capacité de la salle',
                    'class' => 'form-control',
                ],
            ])
            ->add('equipements', ChoiceType::class, [
                'label' => 'Catégories d\'équipements',
                'choices' => [
                    'Appareils de musculation' => 'Appareils de musculation',
                    'Équipements de cardio' => 'Équipements de cardio',
                    'Accessoires d\'entraînement' => 'Accessoires d\'entraînement',
                    'Équipements pour le fitness' => 'Équipements pour le fitness',
                    'Équipements pour les sports collectifs' => 'Équipements pour les sports collectifs',
                    'Équipements pour les sports individuels' => 'Équipements pour les sports individuels',
                    'Équipements pour la récupération' => 'Équipements pour la récupération',
                    'Matériel d\'escalade' => 'Matériel d\'escalade',
                    'Équipements pour les sports aquatiques' => 'Équipements pour les sports aquatiques',
                    'Équipements de yoga et Pilates' => 'Équipements de yoga et Pilates',
                    'Vélos et accessoires' => 'Vélos et accessoires',
                    'Équipements de boxe' => 'Équipements de boxe',
                ],
                'expanded' => true, // Afficher sous forme de cases à cocher
                'multiple' => true, // Autoriser les sélections multiples
                'attr' => [
                    'class' => 'form-check',
                ],
            ])
            ->add('horairesOuverture', TextType::class, [
                'label' => 'Horaires d\'ouverture',
                'attr' => [
                    'placeholder' => 'Exemple : 9h - 18h',
                    'class' => 'form-control',
                ],
            ])
            ->add('contactAdministratif', TextType::class, [
                'label' => 'Contact Administratif',
                'attr' => [
                    'placeholder' => 'Entrez le contact administratif (email ou téléphone)',
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (fichier JPG ou PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG ou PNG).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalleDeSport::class,
        ]);
    }
}
