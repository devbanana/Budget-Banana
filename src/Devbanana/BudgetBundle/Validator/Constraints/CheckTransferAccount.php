<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckTransferAccount extends Constraint
{
    public $message = 'Please select an account to transfer to';
    public $reverseMessage = 'You cannot transfer from an off-budget account to an on-budget account';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

