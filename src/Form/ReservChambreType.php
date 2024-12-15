<?php
namespace App\Form;

use App\Entity\ReservChambre;
use App\Entity\Chambre;
use App\Repository\ChambreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReservChambreType extends AbstractType
{
    private ChambreRepository $chambreRepository;

    public function __construct(ChambreRepository $chambreRepository)
    {
        $this->chambreRepository = $chambreRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère le foyer passé en option
        //$foyer = $options['foyer'];
        
        // Construction du formulaire
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email'
            ])
            ->add('niveauUniversitaire', ChoiceType::class, [
                'label' => 'Niveau Universitaire',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ]
            ])
            ->add('typeChambre', ChoiceType::class, [
                'label' => 'Type de Chambre',
                'choices' => [
                    'Single' => 'single',
                    'Double' => 'double',
                ],
                'placeholder' => 'Choisir le type de chambre',
            ])
            ->add('chambre', EntityType::class, [
                'class' => Chambre::class,
                'choice_label' => 'numeroChambre', // This assumes you want to display the name of the room
                'placeholder' => 'Choisir une chambre',
                'disabled' => false, 
            ])
            ;
        
            // ->add('chambre', EntityType::class, [
            //     'class' => Chambre::class,
            //     'choice_label' => 'numeroChambre',
            //     'label' => 'Numéro de Chambre',
            //     'placeholder' => 'Choisir une chambre',
            //     'query_builder' => function (ChambreRepository $repo) use ($foyer, $options) {
            //         $type = $options['typeChambre'];  // Récupère le type de chambre choisi
            //         return $repo->createQueryBuilder('ch')
            //             ->where('ch.type = :type')
            //             ->andWhere('ch.PlacesDisponibles > 0')
            //             ->setParameter('type', $type);
            //     },
            // ]);
          

        // Pour mettre à jour les chambres disponibles en fonction du type de chambre sélectionné
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     $form = $event->getForm();
        //     $typeChambre = $form->get('typeChambre')->getData();  // Récupère le type de chambre choisi
        //     $form->add('chambre', EntityType::class, [
        //         'class' => Chambre::class,
        //         'choice_label' => 'numeroChambre',
        //         'label' => 'Numéro de Chambre',
        //         'placeholder' => 'Choisir une chambre',
        //         'query_builder' => function (ChambreRepository $repo) use ($typeChambre) {
        //             return $repo->createQueryBuilder('ch')
        //                 ->where('ch.type = :type')
        //                 ->andWhere('ch.PlacesDisponibles > 0')
        //                 ->setParameter('type', $typeChambre);
        //         },
        //     ]);
        // });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservChambre::class,
            //'typeChambre' => 'single',  // Valeur par défaut
            'foyer' => null,            // Valeur par défaut
        ]);
    }
}
