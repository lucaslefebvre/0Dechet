<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required'=>true,
                'label' => 'Titre du commentaire',
                // 'constraints'=> [
                //     new NotBlank([
                //     'message'=>'Ce champ ne doit pas être vide',
                // ])],
            ])
            ->add('content', TextareaType::class, [
                'required'=>true,
                'label' => 'Commentaire',
                // 'constraints'=> [
                //     new NotBlank([
                //     'message'=>'Ce champ ne doit pas être vide',
                // ])],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
