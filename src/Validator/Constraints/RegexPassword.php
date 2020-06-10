<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RegexPassword extends Constraint
{
    public $message = 'Le mot de passe "{{ string }}" n\'est pas valide';
}
