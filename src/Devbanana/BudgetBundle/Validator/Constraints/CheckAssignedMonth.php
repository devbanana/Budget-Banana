<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckAssignedMonth extends Constraint
{
    public $message = 'You must select a month to assign income to.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
