<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('email', EmailType::class, [
                'label'=>'Email',
                'constraints'=> [
                    new NotBlank([
                    'message'=>'Ce champ ne doit pas être vide',
                ]),
                    new Email([
                    'message'=>'L\'email n\'est pas valide'
                ]),
                ]
            ])
            ->add('subject', TextareaType::class, [
                'required'=>true,
                'label' => 'Objet du message',
                'constraints'=> [
                new NotBlank([
                'message'=>'Ce champ ne doit pas être vide',
                ]),
                new Assert\Length
                ([
                'min' => 5,
                'max' => 50,
                'minMessage' => 'L\'objet du message doit contenir au moins 5 caractères',
                'maxMessage' => 'L\'objet du message ne doit pas contenir plus de 50 caractères',
                ]),
            ]])
            ->add('message', TextareaType::class, [
                'required'=>true,
                'label' => 'Message',
                'constraints'=> [
                new NotBlank([
                'message'=>'Ce champ ne doit pas être vide',
                ]),
                new Assert\Length
                ([
                'min' => 5,
                'max' => 1000,
                'minMessage' => 'L\'objet du message doit contenir au moins 5 caractères',
                'maxMessage' => 'L\'objet du message ne doit pas contenir plus de 1000 caractères',
                ])
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
