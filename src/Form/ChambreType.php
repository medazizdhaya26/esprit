<?php
namespace App\Form;

use App\Entity\Chambre;
use App\Entity\Foyer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroChambre', null, [
                'label' => 'Numéro de Chambre',
                'attr' => ['placeholder' => 'Ex : 01'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d{2}$/',
                        'message' => 'Le numéro de chambre doit être composé de deux chiffres uniquement'
                    ]),
                ]
            ])
        
            ->add('type', ChoiceType::class, [
                'label' => 'Type de Chambre',
                'choices' => [
                    'Single' => 'single',
                    'Double' => 'double',
                ],
                'placeholder' => 'Choisissez un type',
            ])
            ->add('climatiseur', CheckboxType::class, [
                'label' => 'Climatiseur',
                'required' => false,
            ])
            ->add('chauffageCentral', CheckboxType::class, [
                'label' => 'Chauffage Central',
                'required' => false,
            ])
            ->add('estDisponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
            ])
            ->add('foyer', EntityType::class, [
                'class' => Foyer::class,
                'choice_label' => 'nomFoyer',
                'placeholder' => 'Choisissez un foyer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
