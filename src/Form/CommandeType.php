<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse', TextType::class)
            ->add('commentaire', TextareaType::class, [
                'required' => false,
            ])
            ->add('mode_paiement', ChoiceType::class, [
                'choices' => [
                   
                    'cash à la livraison' => 'cash à la livraison',
                    'Carte Bancaire' => 'Carte Bancaire',
                    'Virement Bancaire' => 'Virement Bancaire',
                ],
            ])
           
            ->add('submit', SubmitType::class, [
                'label' => 'Commander'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
