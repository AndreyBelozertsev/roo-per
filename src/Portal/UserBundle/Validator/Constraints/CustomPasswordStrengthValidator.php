<?php

namespace Portal\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Password strength Validation.
 */
class CustomPasswordStrengthValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($password, Constraint $constraint)
    {
        if (null !== $password && !is_scalar($password) && !(is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }
      
        // Get password
        $password = (string) $password;
        
        if (trim($password) == "") {
            $this->context->addViolation($constraint->message);

            return;
        }
    }
}
