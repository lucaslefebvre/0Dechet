<?php 

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegexPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (preg_match('/(admin..entity)/', $_SERVER['REQUEST_URI'], $matches)) {

            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,20})$/', $value, $matches)) {
                // the argument must be a string or an object implementing __toString()
                $this->context->buildViolation('Le mot de passe n\'est pas valide')
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}
