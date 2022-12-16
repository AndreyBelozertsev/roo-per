<?php

namespace Portal\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CustomPasswordStrength extends Constraint
{
    public $message = 'users_form.validation.password';
}
