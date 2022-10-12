<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ContainsInArrayValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsInArray) {
            throw new UnexpectedTypeException($constraint, ContainsInArray::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $forbidden = false;
        foreach($constraint->forbiddenvalues as $forbiddenvalues)
        {
            if(in_array($forbiddenvalues, $value))
            {
               $forbidden = true;
               $string = $forbiddenvalues;
               break; 
            }
        }

        if($forbidden)
        {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $string)
                ->addViolation();
        }
    }
}