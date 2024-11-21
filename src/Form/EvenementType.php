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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreevets', TextType::class, [
                'label' => 'Title',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Title cannot be blank.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Title must be at least {{ limit }} characters long.',
                        'max' => 255,
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Description cannot be blank.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('date_debut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date and Time',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Start date cannot be blank.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'Start date must be today or later.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('date_fin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'End Date and Time',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'End date cannot be blank.',
                    ]),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[date_debut].data',
                        'message' => 'End date must be after the start date.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Location',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Location cannot be blank.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('nombre_participants_max', IntegerType::class, [
                'label' => 'Number of Places',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Number of places cannot be blank.',
                    ]),
                    new Assert\GreaterThan([
                        'value' => 0,
                        'message' => 'Number of places must be greater than 0.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type_events', ChoiceType::class, [
                'choices'  => [
                    'Sport' => 'sport',
                    'Tech' => 'tech',
                    'Loisir' => 'loisir',
                    'IT' => 'it',
                    'Workshop' => 'workshop',
                    'Dance' => 'dance',
                    'Entrepreneurship' => 'entreprenatiat',
                ],
                'label' => 'Event Type',
                'constraints' => [
                    new Assert\Choice([
                        'choices' => ['sport', 'tech', 'loisir', 'it', 'workshop', 'dance', 'entreprenatiat'],
                        'message' => 'Please select a valid event type.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
