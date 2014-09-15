<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckCategory extends Constraint
{
    public $message = 'Please select a category.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

