<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
 
        $builder
            ->add('email',EmailType::class,
                [
                'label'=>'Votre adresse email',
                'help' => 'Merci d\'indiquer une adresse email valide afin que nous puissions répondre à votre message',

                'constraints'=> [
                new NotBlank([
                'message'=>'Ce champ ne doit pas être vide',
                ]),
                new Email([
                'message'=>'L\'email n\'est pas valide'
                ]),
                ]]
                )
            ->add('subject', TextType::class, [
                'required'=>true,
                'label' => 'Objet de votre message',
                'help' => 'L\'objet de votre message doit contenir au minimum 5 caractères et au maximum 50 caractères',
                'constraints'=> [
                new NotBlank([
                'message'=>'Ce champ ne doit pas être vide',
                ]),
                new Assert\Length([
                'min' => 5,
                'max' => 50,
                'minMessage' => 'L\'objet du message doit contenir au moins 5 caractères',
                'maxMessage' => 'L\'objet du message ne doit pas contenir plus de 50 caractères',
                ]),
            ]])
            ->add('message', TextareaType::class, [
                'required'=>true,
                'help' => 'Votre message doit contenir au minimum 5 caractères et au maximum 1000 caractères',
                'label' => 'Votre message',
                'constraints'=> [
                new NotBlank([
                'message'=>'Ce champ ne doit pas être vide',
                ]),
                new Assert\Length([
                'min' => 5,
                'max' => 1000,
                'minMessage' => 'Le contenu du message doit contenir au moins 5 caractères',
                'maxMessage' => 'L\'objet du message ne doit pas contenir plus de 1000 caractères',
                ])
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
