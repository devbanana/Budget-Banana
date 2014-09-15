<?php

namespace Devbanana\BudgetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckInflowOutflow extends Constraint
{
    public $neitherMessage = 'You must enter either an inflow or an outflow';
    public $bothMessage = 'You can only enter one of either inflow or outflow';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
