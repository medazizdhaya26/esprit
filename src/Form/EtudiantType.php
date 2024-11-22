<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new Assert\NotBlank(["message" => "The name is required"]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'The name cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
                'constraints' => [
                    new Assert\NotBlank(["message" => "The first name is required"]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'The first name cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Phone',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{9,15}$/',
                        'message' => 'The phone number must be valid',
                    ]),
                ],
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\LessThan([
                        'value' => 'today',
                        'message' => 'The date of birth must be in the past',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(["message" => "The email is required"]),
                    new Assert\Email([
                        'message' => "The email address is not valid",
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 20,
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W]).{8,}/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])
            ->add('payement', ChoiceType::class, [
                'label' => 'Payment',
                'choices' => [
                    'Yes' => '1',
                    'No' => '0',
                ],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotNull(["message" => "The payment status is required"]),
                ],
            ])
            ->add('filiere', TextType::class, [
                'label' => 'Field of Study',
                'constraints' => [
                    new Assert\NotBlank(["message" => "The field of study is required"])

                ],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Profile Picture',
                'constraints' => [
                    new Image([
                        'maxSize' => '25M',
                        'mimeTypesMessage' => 'Please upload a valid image (JPG, PNG, GIF, etc.)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
