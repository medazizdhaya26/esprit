<?php

namespace App\Form;

use App\Entity\Livre;
// use App\Enum\CategorieEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du livre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('nombrePages', IntegerType::class, [
                'label' => 'Nombre de pages',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('datePublication', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Roman' => 'roman',
                    'Science-fiction' => 'science_fiction',
                    'Histoire' => 'histoire',
                    'Philosophie' => 'philosophie',
                    'Biographie' => 'biographie',
                ],
                'expanded' => false,  // pour une liste déroulante
                'multiple' => false,  // pour une seule sélection
                'attr' => ['class' => 'form-control']
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['class' => 'form-control']
            ])
            ->add('langue', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Arabe' => 'arabe',
                    'Français' => 'francais',
                    'Anglais' => 'anglais',
                    'Espagnol' => 'espagnol',
                    'Italien' => 'italien',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}