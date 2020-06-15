<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // This is for set a new password
            ->add('plainPassword', RepeatedType::class,[
                'type'=>PasswordType::class,
                'required'=>false,
                'help'=>'Votre mot de passe doit être compris entre 8 et 20 caractères et doit contenir au moins une minuscle,
                une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                'mapped'=>false,
                'first_options'=>[
                    'label'=>'Mot de passe',
                ],
                'second_options'=>[
                    'label'=>'Retaper le mot de passe',
                ],
                'invalid_message' => 'Les deux mots de passe ne correspondent pas',
                'constraints'=> [
                    new NotBlank([
                    'allowNull'=>true,
                    'normalizer'=>'trim',
                    'message'=>'Ce champ ne doit pas être vide',
                    ]),
                    new Regex([
                        'pattern'=>'/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,20})$/',
                        'message' => 'Votre mot de passe doit être compris entre 8 et 20 caractères et doit contenir au moins une minuscle,
                         une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
