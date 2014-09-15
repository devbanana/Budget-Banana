<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckCategoryValidator extends ConstraintValidator
{
    public function validate($lineItem, Constraint $constraint)
    {
        if (!$lineItem->getAccount()) {
            // If no account is selected, we can't continue
            return;
        }

        // If type is expense and account is on-budget, category is required
        if ($lineItem->getType() == 'expense'
                && $lineItem->getAccount()->getBudgeted() == true
                && !$lineItem->getCategory()) {
            $this->context->addViolationAt('category', $constraint->message);
        }

        // If type is transfer, account is on-budget and transfer account
        // is off-budget, category is required
        if ($lineItem->getType() == 'transfer'
                && $lineItem->getAccount()->getBudgeted() == true
                && $lineItem->getTransferAccount()
                && $lineItem->getTransferAccount()->getBudgeted() == false) {
            $this->context->addViolationAt('category', $constraint->message);
        }
    }
}
