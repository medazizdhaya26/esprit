<?php

namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTime(); // Date d'aujourd'hui

        $builder
            ->add('nom', ChoiceType::class, [
                'choices' => [
                    'Mensuel' => 'Mensuel',
                    'Trimestriel' => 'Trimestriel',
                    'Annuel' => 'Annuel',
                ],
                'label' => 'Type d\'abonnement',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type d\'abonnement.',
                    ]),
                ],
            ])
            ->add('prix', TextType::class, [
                'label' => 'Prix',
                'attr' => ['readonly' => true], // Le prix est en lecture seule
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de début est obligatoire.',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => $today,
                        'message' => 'La date de début doit être égale ou supérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de fin est obligatoire.',
                    ]),
                    new Callback([
                        'callback' => function ($dateFin, ExecutionContextInterface $context) {
                            $form = $context->getRoot(); // Récupérer le formulaire racine
                            $dateDebut = $form->get('dateDebut')->getData();

                            if ($dateDebut && $dateFin <= $dateDebut) {
                                $context->buildViolation('La date de fin doit être après la date de début.')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ]);

        // Ajout d'un écouteur d'événements pour calculer le prix en fonction du type d'abonnement
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Calcul du prix en fonction du type d'abonnement
            switch ($data['nom']) {
                case 'Mensuel':
                    $data['prix'] = 50; // Exemple de prix pour un abonnement mensuel
                    break;
                case 'Trimestriel':
                    $data['prix'] = 150; // Exemple de prix pour un abonnement trimestriel
                    break;
                case 'Annuel':
                    $data['prix'] = 500; // Exemple de prix pour un abonnement annuel
                    break;
                default:
                    $data['prix'] = 0; // Par défaut, aucun prix
                    break;
            }

            // Mise à jour des données
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
