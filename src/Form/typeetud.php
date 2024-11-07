<?php

namespace App\Form;

use App\Entity\Bibliotheque;
use App\Entity\Club;
use App\Entity\Etudiant;
use App\Entity\Restaurant;
use App\Entity\SalleDeSport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class typeetud extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email') // Required by default

            ->add('password') // Required by default
            ->add('filiere') // Required by default
            ->add('payement', null, [
                'required' => false, // Optional field
            ])
            ->add('bibliotheque', EntityType::class, [
                'class' => Bibliotheque::class,
                'choice_label' => 'id',
                'required' => false, // Optional field
            ])
            ->add('salleDeSport', EntityType::class, [
                'class' => SalleDeSport::class,
                'choice_label' => 'id',
                'required' => false, // Optional field
            ])
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => 'id',
                'required' => false, // Optional field
            ])
            ->add('clubs', EntityType::class, [
                'class' => Club::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false, // Optional field
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
