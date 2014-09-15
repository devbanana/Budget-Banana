<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckInflowOutflowValidator extends ConstraintValidator
{
    public function validate($lineItem, Constraint $constraint)
    {
        if (bccomp($lineItem->getInflow(), '0.00', 2) == 0
                && bccomp($lineItem->getOutflow(), '0.00', 2) == 0) {
            $this->context->addViolation($constraint->neitherMessage);
        }

        if (bccomp($lineItem->getInflow(), '0.00', 2) != 0
                && bccomp($lineItem->getOutflow(), '0.00', 2) != 0) {
            $this->context->addViolation($constraint->bothMessage);
        }

    }
}
