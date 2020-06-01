<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'constraints' => new Assert\NotBlank(),
                'constraints' => [
                new Assert\Length
                ([
                'min' => 5,
                'max' => 35,
                'minMessage' => 'Le nom de la recette doit contenir au moins 5 caractères',
                'maxMessage' => 'Le nom de la recette ne doit pas contenir plus de 35 caractères',
                ]),
            ]])

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
                'constraints' => [
                new Assert\Length
                ([
                'min' => 30,
                'minMessage' => 'La recette doit contenir au moins 30 caractères',
                ])],
            ])
            ->add('duration', ChoiceType::class,  [
                'placeholder' => 'Sélectionner la durée',
                'choices' => [
                    '0 mn' => '0',
                    '15 mn' => '15',
                    '30 mn' => '30',
                    '45 mn' => '45',
                    '1 h 00' => '60',
                    '1 h 15' => '75',
                    '1 h 30' => '90',
                    '1 h 45' => '105',
                    '2 h 00' => '120',
                ],
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'label' => 'Durée',
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
                    '1 semaine' => '7',
                    '2 semaines' => '14',
                    '1 mois' => '30',
                    '2 mois' => '60',
                    '3 mois' => '90',
                    '6 mois' => '180'],
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'label' => 'Temps le conservation',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['placeholder' => 'Sélectionner votre fichier']
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
