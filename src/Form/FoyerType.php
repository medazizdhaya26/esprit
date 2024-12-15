<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Foyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoyerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('nomFoyer', null, [
            'label' => 'Nom du Foyer',
           ])

            ->add('nombreEtage', null, [
                'label' => 'Nombre d\'Étages',
            ])
            ->add('lieuFoyer', null, [
                'label' => 'Lieu',
            ])
            ->add('nombreChambresSingle', null, [
                'label' => 'Nombre de Chambres Simples',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de chambres simples ne peut pas être vide.']),
                    new Assert\GreaterThanOrEqual(['value' => 0, 'message' => 'Le nombre de chambres simples ne peut pas être négatif.']),
                ]
                
            ])
            ->add('nombreChambresDouble', null, [
               'label' => 'Nombre de Chambres Doubles',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de chambres doubles ne peut pas être vide.']),
                    new Assert\GreaterThanOrEqual(['value' => 0, 'message' => 'Le nombre de chambres doubles ne peut pas être négatif.']),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Foyer::class,
        ]);
    }
}
