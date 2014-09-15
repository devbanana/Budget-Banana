<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckTransferAccountValidator extends ConstraintValidator
{
    public function validate($lineItem, Constraint $constraint)
    {
        if ($lineItem->getType() == 'transfer') {
            if (!$lineItem->getTransferAccount()) {
                $this->context->addViolationAt('transferAccount',
                        $constraint->message);
            }
            else {
                if ($lineItem->getAccount()
                        && $lineItem->getAccount()->getBudgeted() == false
                        && $lineItem->getTransferAccount()->getBudgeted() == true) {
                    $this->context->addViolation($constraint->reverseMessage);
                }
            }
        }
    }
}
