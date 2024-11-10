<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
            ])
            ->add('password', TextType::class, [
                'label' => 'Mot de passe',
            ])
            ->add('payement', ChoiceType::class, [
                'label' => 'Payement',
                'choices' => [
                    'Yes' => '1','No' => '0',
                ],
                'expanded' => true,
                'multiple' => false,
                ])
            ->add('filiere', TextType::class, [
                'label' => 'filiere',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
