<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventuserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreevets')
            ->add('description')
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('lieu')
            ->add('nombre_participants_max')
            ->add('type_events', ChoiceType::class, [
                'choices'  => [
                    'Sport' => 'sport',
                    'Tech' => 'tech',
                    'Loisir' => 'loisir',
                    'IT' => 'it',
                    'Workshop' => 'workshop',
                    'dance' => 'uni',
                    'entreprenatiat' => 'entreprenatiat',
                ],
                'label' => 'Type d\'événement',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
