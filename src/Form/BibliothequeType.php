<?php

namespace App\Form;


use App\Entity\Bibliotheque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class BibliothequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la bibliothèque',
                'attr' => ['placeholder' => 'Entrez le nom de la bibliothèque']
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité',
                'attr' => ['placeholder' => 'Entrez la capacité en nombre de places']
            ])
            ->add('contactAdministratif', TextType::class, [
                'label' => 'Contact administratif',
                'attr' => ['placeholder' => 'Entrez l\'email ou le numéro de téléphone']
            ])
            ->add('horairesOuverture', ChoiceType::class, [
                'label' => 'Horaires d\'ouverture',
                'choices' => [
                    'Lundi, ' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ],
                'expanded' => true,  // Use checkboxes
                'multiple' => true,  // Allow multiple choices
                'data' => $builder->getData()->getHorairesOuverture() ? explode(', ', $builder->getData()->getHorairesOuverture()) : [],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false, 
                'attr' => ['placeholder' => 'Entrez une description (facultatif)']
            ])
           
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bibliotheque::class,
        ]);
    }
}
