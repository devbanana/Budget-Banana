<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckAssignedMonthValidator extends ConstraintValidator
{
    public function validate($lineItem, Constraint $constraint)
    {
        if ($lineItem->getType() == 'income') {
            // If account is on-budget, assignedMonth is required
            if ($lineItem->getAccount() && $lineItem->getAccount()->getBudgeted()) {
                if (!$lineItem->getAssignedMonth()) {
                    $this->context->addViolation($constraint->message, array());
                }
            }
        }
    }

}

