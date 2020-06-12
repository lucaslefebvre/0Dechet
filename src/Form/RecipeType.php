<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
            [   
                'label' => 'Nom de la recette',
                'required' => true,
                'help' => 'Le nom de la recette doit être compris entre 5 et 50 caractères'
            ])

            ->add('ingredient', CollectionType::class, [
                'required' => true,
                'label' => false,
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('equipement', CollectionType::class, [
                'label' => false,
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Détail de la préparation',
                'help' => 'Le détail de la préparation doit contenir au moins 30 caractères',
                ]
            )
            ->add('duration', IntegerType::class, [
                'required' => true,
                'label' => 'Durée',
                'help' => 'Merci d\'indiquer la durée de la préparation en minutes',
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'placeholder' => 'Sélectionner le niveau',
                'choices' => [
                    'Débutant' => '1',
                    'Intérmediaire' => '2',
                    'Expert' => '3',
                ],
                'multiple' => false,
                'expanded' => false,
                'required' => true,
            ])
            ->add('conservation', ChoiceType::class,  [
                'placeholder' => 'Sélectionner le temps de conservation',
                'choices' => [
                    'Aucune limite de conservation' => '0',
                    '1 semaine' => '7',
                    '2 semaines' => '14',
                    '1 mois' => '30',
                    '2 mois' => '60',
                    '3 mois' => '90',
                    '6 mois' => '180',
                    ],
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'label' => 'Temps de conservation',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'La photo doit faire 4Mo maximum',
                'attr' => ['placeholder' => 'Sélectionner votre fichier']
            ])
            ->add('video', UrlType::class, [
                'label' => 'Vidéo',
                'required' => false,
                'mapped' => false,
                'help' => 'Copier le lien Youtube, Dailymotion ou Vimeo de votre vidéo'
            ])

            ->add('type', EntityType::class, [
                'class' => Type::class,
                'required' => true,
                'label' => "Type de recette",
                'placeholder' => 'Sélectionner le type',
                'group_by' => function(Type $type) {
                    return $type->getSubCategory()->getName();
                }
            ]);
    
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
