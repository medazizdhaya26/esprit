<?php

namespace App\Form;

use App\Entity\Bibliotheque;
use App\Entity\Club;
use App\Entity\Enseignant;
use App\Entity\Restaurant;
use App\Entity\SalleDeSport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('birthday', null, [
                'widget' => 'single_text',
            ])
            ->add('telephone')
            ->add('matiere')
            ->add('salaire')
            ->add('clubs', EntityType::class, [
                'class' => Club::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('bibliotheque', EntityType::class, [
                'class' => Bibliotheque::class,
                'choice_label' => 'id',
            ])
            ->add('salleDeSport', EntityType::class, [
                'class' => SalleDeSport::class,
                'choice_label' => 'id',
            ])
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
