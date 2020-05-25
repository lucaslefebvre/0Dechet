<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;


class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
            ['constraints' => new NotBlank(),
             'constraints' => [
                new Assert\Length
                ([
                'min' => 5,
                'max' => 35,
                'minMessage' => 'Le nom de la recette doit contenir au moins 5 caractères',
                'maxMessage' => 'Le nom de la recette ne doit pas contenir plus de 35 caractères',
                ]),
            ]])
            ->add('ingredient',)
            ->add('equipement', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'prototype_data' => 'New Tag Placeholder',
            ])
            ->add('content', TextareaType::class)
            ->add('duration')
            ->add('difficulty')
            ->add('conservation')
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('status')
            ->add('type')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
